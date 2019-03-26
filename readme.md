## Install
- composer install
- php artisan storage:link

## Run
- php artisan serve

When you open the startup page you will see these options:
-Upload file
[MAX 2MB]
[File format must be .txt]

-Get file via URL link
[The url link must point to an existing txt file]

-Recently uploaded files

(Note that in the default php.ini file the "Max Uploaded File Size" is 2MB so that is why I limited the max file size so in every enviroment will work in the same way.)
If you wish to upload bigger files than 2MB then the files stored in "storage/app/public" which has a soft link at "public/storage". You can copy your file in either of these locations and your file will appear in the "Recently Uploadd Files" list.

## What I considered word in this programm
Word is a chain of characters between spaces.
First of all I remove all the unneccesary characters including line breaks (\n\r) from the text except:
-and (&)
-dot (.)
The dots will be removed from the end of sentences except if it is in a word like dates (12.03.2019)
-forward slash (/)
For different date formats(12/03/2019)
-dash (-)
For different date formats(12-03-2019)