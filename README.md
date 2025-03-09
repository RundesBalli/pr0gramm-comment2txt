# pr0gramm-comment2txt
CLI-Tool to export all comments of a specified user on the German imageboard pr0gramm.com.

## Dependencies
- [pr0gramm-apiCall](https://github.com/RundesBalli/pr0gramm-apiCall)

You have to modify the apiCall path in the script!

## Using the script
This is a CLI script, therefore it has to be executed in the terminal and not in the browser.  
Example call:  
```bash
php ./comment2txt.php RundesBalli 29
```
The user `RundesBalli` will be crawled with flags `29` (SFW+NSFL+NSFP+POL).  

## Flags
See list below. You have to add the numbers to combine. So SFW+NSFP will be 9.
```php
$flagNames = [
  1  => 'SFW',
  2  => 'NSFW',
  4  => 'NSFL',
  8  => 'NSFP',
  16 => 'POL',
];
```

## Output
The script creates a `.txt` file in the working directory:  
```
comments_{USERNAME}_f{FLAGS}_{Ymd_His}.txt
```
