#!/bin/sh

# Obtener la hora actual
current_time=$(date +"%H:%M:%S")

# Mensaje de advertencia
message="test: Hello, world!!"

# Imprimir la hora y el mensaje en la salida estándar
echo "${current_time}: ${message}"