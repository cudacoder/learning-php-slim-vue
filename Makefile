init:
	touch src/backend/db.sqlite
	chmod 660 src/backend/db.sqlite
	chown nginx src/backend/db.sqlite