#!/bin/bash

# Tanggal saat ini
currentDate=$(date +"%Y-%m-%d")

# Nama file
fileName="laravel-$currentDate.log"

# Path lokasi penyimpanan file
filePath="/var/www/html/sobermart.id/storage/logs/$fileName"

# Cek apakah file sudah ada
if [ ! -f "$filePath" ]; then
    touch "$filePath"
    echo "File baru berhasil dibuat: $fileName"
    chown www-data:www-data "$filePath"  # Mengubah pemilik file menjadi www-data
    chmod 777 "$filePath"  # Memberikan izin rw-r--r-- pada file
else
    echo "File sudah ada: $fileName"
    chown www-data:www-data "$filePath"  # Mengubah pemilik file menjadi www-data
    chmod 777 "$filePath"  # Memberikan izin rw-r--r-- pada file
fi
