#!/bin/bash -e

#Change the permission of the directories to 775, and files to 664
#These permission settings have been confirmed by the client

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
			chmod 664 $f

		fi
		chgrp tkbtswww $f
	done
}

changePerm
chmod +x allow_permissions.sh