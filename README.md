# Translation tools
Export and import xls to php or yml file


	php artisan translation:export -d [path_to_lang_dir] -l en -l fr -l es -l nl --ref ref -o [path_to_export_file_with_the_name]
	php artisan translation:import -o [path_to_lang_dir] -l en -l fr -l es -l nl -f [path_to_export_file_with_the_name]

