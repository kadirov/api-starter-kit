#!/bin/bash

set -e

# Read environment variables
DOMAIN="${MAILSERVER_DOMAINNAME:-example.com}"
SELECTOR="${DKIM_SELECTOR:-dmail}"
FULL_EMAIL="${MAILSERVER_EMAIL:-no-reply@example.com}"
EMAIL_PASSWORD="${MAILSERVER_EMAIL_PASSWORD:-nNdh48jdJhHmvf}"

# Paths
KEY_DIR="/tmp/docker-mailserver/opendkim/keys/$DOMAIN"
PRIVATE_KEY="$KEY_DIR/$SELECTOR.private"
TXT_FILE="$KEY_DIR/$SELECTOR.txt"
OUTPUT_DIR="/docker/mailserver/dkim"

echo "[init-dkim] Domain: $DOMAIN"
echo "[init-dkim] Selector: $SELECTOR"
echo "[init-dkim] Email: $FULL_EMAIL"

# Generate DKIM keys if they don't exist
if [ ! -f "$PRIVATE_KEY" ]; then
  echo "[init-dkim] DKIM keys not found, generating..."

  mkdir -p "$KEY_DIR"
  opendkim-genkey -b 2048 -d "$DOMAIN" -s "$SELECTOR" -D "$KEY_DIR"

  # Set permissions
  chown opendkim:opendkim "$PRIVATE_KEY"
else
  echo "[init-dkim] DKIM keys already exist, skipping generation."
fi

# Export unmodified TXT record for DNS
if [ -f "$TXT_FILE" ]; then
  echo "[init-dkim] Copying raw TXT record to $OUTPUT_DIR/${SELECTOR}.txt"
  mkdir -p "$OUTPUT_DIR"

  cp "$TXT_FILE" "$OUTPUT_DIR/${SELECTOR}.txt"

  echo "[init-dkim] DKIM record saved to $OUTPUT_DIR/${SELECTOR}.txt"
  echo
else
  echo "[init-dkim] ERROR: TXT file not found at $TXT_FILE"
  exit 1
fi

# Create email account if not already present (check via command)
if setup email list | grep -q "^$FULL_EMAIL$"; then
  echo "[init-dkim] Mail account $FULL_EMAIL already exists, skipping creation."
else
  echo "[init-dkim] Creating mail account with setup script..."
  setup email add "$FULL_EMAIL" "$EMAIL_PASSWORD" || echo "[init-dkim] Warning: setup email add failed (possibly already exists)"
  echo "[init-dkim] Mail account created or already existed: $FULL_EMAIL"
fi