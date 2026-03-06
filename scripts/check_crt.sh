#!/bin/bash
DIR="$(cd "$(/usr/bin/dirname "${BASH_SOURCE[0]}")" && /bin/pwd)"
cd "$DIR/../vendor/simplesamlphp/simplesamlphp/cert"

check_crt(){
	PEM=$1
	DAYS=3024000
	_openssl="/usr/bin/openssl"

	exp=$(openssl x509 -enddate -noout -in "$PEM" -checkend "$DAYS") || echo "Warning: The TLS/SSL certificate ($PEM) will expire soon [$exp]"
}
export -f check_crt
ERRORS=$(find . -name "*.crt" -exec bash -c 'check_crt "$1"' bash {} \;)

if [ -n "${ERRORS}" ]; then
  echo "SPID-CIE CERTIFICATES EXPIRING:"
  echo -e "${ERRORS}"
else
  echo "SPID-CIE CERTIFICATES OK"
fi
