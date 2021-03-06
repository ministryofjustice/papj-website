#!/bin/sh

# Configure the $maintenance_mode variable used by nginx in server.conf
# Setting the value to 1 will trigger the maintenance page

OUTFILE="/etc/nginx/maintenance_mode.conf"

generate_maintenance_mode_var() {
	local DEFAULT=${1}
	local WHITELIST_IPS=${2}

	echo "# This file was auto-generated by the init script 'configure-maintenance-mode.sh'."

	echo "geo \$maintenance_mode {"

	# Default value for $maintenance_mode
	echo "    default $DEFAULT;"

	# For whitelisted IPs, $maintenance_mode = 0
	for IP in $(echo $WHITELIST_IPS | tr "," "\n")
	do
		echo "    ${IP} 0;"
	done

	echo "}"
}

if [ "$MAINTENANCE_MODE" = "1" ]
then
	echo "Enabling maintenance mode because env variable MAINTENANCE_MODE = $MAINTENANCE_MODE"
	generate_maintenance_mode_var 1 "$MAINTENANCE_WHITELIST_IPS" > $OUTFILE
else
	echo "No need to enable maintenance mode – disabling it"
	generate_maintenance_mode_var 0 > $OUTFILE
fi
