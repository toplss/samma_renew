#!/bin/bash

SOURCE_DIR="/home/samma_renew/www"
BACKUP_DIR="/home/app_backup"

DATE=$(date +"%Y%m%d")
BACKUP_FILE="$BACKUP_DIR/samma_renew_$DATE.zip"

# 백업 폴더 없으면 생성
mkdir -p "$BACKUP_DIR"

# 이미 있으면 중복 생성 방지
if [ ! -f "$BACKUP_FILE" ]; then
    zip -r -9 "$BACKUP_FILE" "$SOURCE_DIR"
fi

# 10일 지난 파일 삭제
find "$BACKUP_DIR" -name "samma_renew_*.zip" -mtime +10 -type f -delete
