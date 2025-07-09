#!/bin/bash

set -e

# Read environment variables
DOMAIN="${MAILSERVER_DOMAINNAME:-example.com}"
SELECTOR="${DKIM_SELECTOR:-dmail}"
FULL_EMAIL="${MAILSERVER_EMAIL:-no-reply@example.com}"
EMAIL_PASSWORD="${MAILSERVER_EMAIL_PASSWORD:-nNdh48jdJhHmvf}"

# Secure key location
KEY_DIR="/etc/opendkim/keys/$DOMAIN"
PRIVATE_KEY="$KEY_DIR/$SELECTOR.private"
TXT_FILE="$KEY_DIR/$SELECTOR.txt"
OUTPUT_DIR="/docker/mailserver/dkim"

SIGNING_TABLE="/etc/opendkim/SigningTable"
KEY_TABLE="/etc/opendkim/KeyTable"
TRUSTED_HOSTS="/etc/opendkim/TrustedHosts"

echo "[init-dkim] Domain: $DOMAIN"
echo "[init-dkim] Selector: $SELECTOR"
echo "[init-dkim] Email: $FULL_EMAIL"

# Generate DKIM keys if they don't exist
if [ ! -f "$PRIVATE_KEY" ]; then
  echo "[init-dkim] DKIM keys not found, generating..."

  mkdir -p "$KEY_DIR"
  opendkim-genkey -b 2048 -d "$DOMAIN" -s "$SELECTOR" -D "$KEY_DIR"

  # Set secure permissions
  chown -R opendkim:opendkim "$KEY_DIR"
  chmod 700 "$KEY_DIR"
  chmod 400 "$PRIVATE_KEY"
else
  echo "[init-dkim] DKIM keys already exist, skipping generation."
fi

# Export unmodified TXT record for DNS
if [ -f "$TXT_FILE" ]; then
  echo "[init-dkim] Copying raw TXT record to $OUTPUT_DIR/${SELECTOR}.txt"
  mkdir -p "$OUTPUT_DIR"
  cp "$TXT_FILE" "$OUTPUT_DIR/${SELECTOR}.txt"
else
  echo "[init-dkim] ERROR: TXT file not found at $TXT_FILE"
  exit 1
fi

# Create email account if not already present
if setup email list | grep -qx "$FULL_EMAIL"; then
  echo "[init-dkim] Mail account $FULL_EMAIL already exists, skipping creation."
else
  echo "[init-dkim] Creating mail account with setup script..."
  setup email add "$FULL_EMAIL" "$EMAIL_PASSWORD" || echo "[init-dkim] Warning: setup email add failed (possibly already exists)"
  echo "[init-dkim] Mail account created or already existed: $FULL_EMAIL"
fi

# === BLOCK: SigningTable, KeyTable, TrustedHosts ===
echo "[init-dkim] Ensuring SigningTable, KeyTable, TrustedHosts entries exist"

mkdir -p "$(dirname "$SIGNING_TABLE")"
touch "$SIGNING_TABLE" "$KEY_TABLE" "$TRUSTED_HOSTS"

# Add to SigningTable if missing
if ! grep -E -q "^.*@$DOMAIN[[:space:]]+$SELECTOR._domainkey.$DOMAIN" "$SIGNING_TABLE"; then
  echo "*@$DOMAIN    $SELECTOR._domainkey.$DOMAIN" >> "$SIGNING_TABLE"
  echo "[init-dkim] Added to SigningTable"
else
  echo "[init-dkim] SigningTable entry already exists"
fi

# Add to KeyTable if missing
if ! grep -E -q "^$SELECTOR._domainkey.$DOMAIN[[:space:]]+$DOMAIN:$SELECTOR:$PRIVATE_KEY" "$KEY_TABLE"; then
  echo "$SELECTOR._domainkey.$DOMAIN    $DOMAIN:$SELECTOR:$PRIVATE_KEY" >> "$KEY_TABLE"
  echo "[init-dkim] Added to KeyTable"
else
  echo "[init-dkim] KeyTable entry already exists"
fi

# Add to TrustedHosts if missing
for host in "127.0.0.1" "localhost" "$DOMAIN"; do
  if ! grep -Fxq "$host" "$TRUSTED_HOSTS"; then
    echo "$host" >> "$TRUSTED_HOSTS"
    echo "[init-dkim] Added $host to TrustedHosts"
  else
    echo "[init-dkim] $host already in TrustedHosts"
  fi
done

echo "[init-dkim] DKIM config tables ensured ✅"

# === Check and install opendkim-tools ===
if ! command -v opendkim-testkey >/dev/null 2>&1; then
  echo "[init-dkim] opendkim-testkey not found. Installing opendkim-tools..."
  apt-get update && apt-get install -y opendkim-tools
else
  echo "[init-dkim] opendkim-testkey is already installed"
fi

# === Validate DKIM key ===
echo "[init-dkim] Validating DKIM key..."
if opendkim-testkey -d "$DOMAIN" -s "$SELECTOR" -k "$PRIVATE_KEY" -vvv | grep -q "key OK"; then
  echo "[init-dkim] ✅ DKIM key is valid"
else
  echo "[init-dkim] ❌ DKIM key validation failed"
  exit 1
fi
