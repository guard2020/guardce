function wait_for_logs_occurs () {
	APP=$1
	MSG=$2
	echo "Waiting for \"$APP\" logs to occurs \"$MSG\""
	COUNTER_MAX=120
	COUNTER=$COUNTER_MAX
	while [ $COUNTER -ge 0 ]
	do
	  	if [ $(docker logs $APP 2>&1 | grep -F "$MSG" | wc -l) -eq 1 ]
		then
			return
		fi
		sleep 1
		let "COUNTER-=1"
	done
	
	echo "Cannot find \"$MSG\" in \"$APP\" logs after $COUNTER_MAX seconds. Check logs for errors."
	exit -1
}

function download_fabric_binaries () {
	if [ ! -f bin/fabric-ca-client ] || [ ! -f bin/configtxgen ]
	then
		ARCH=$(echo "$(uname -s|tr '[:upper:]' '[:lower:]'|sed 's/mingw64_nt.*/windows/')-$(uname -m | sed 's/x86_64/amd64/g')")
		VERSION=2.3.1
		CA_VERSION=1.4.9
		BINARY_FILE=hyperledger-fabric-${ARCH}-${VERSION}.tar.gz
		CA_BINARY_FILE=hyperledger-fabric-ca-${ARCH}-${CA_VERSION}.tar.gz

		curl -L --retry 5 --retry-delay 3 "https://github.com/hyperledger/fabric/releases/download/v${VERSION}/${BINARY_FILE}" | tar xzm bin --no-overwrite-dir
		curl -L --retry 5 --retry-delay 3 "https://github.com/hyperledger/fabric-ca/releases/download/v${CA_VERSION}/${CA_BINARY_FILE}" | tar xzm --no-overwrite-dir
	fi
}