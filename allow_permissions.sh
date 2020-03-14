#!/bin/bash -e

#Change the permission of the directories to 775, and files to 664

changePerm() {
	for f in ./*
	do
		if [[ -d $f ]]
		then
			chmod 775 $f
			cd $f
			changePerm
			cd ..
		else
			chmod 644 $f

		fi
	done
}

changePerm
chmod +x allow_permissions.sh
