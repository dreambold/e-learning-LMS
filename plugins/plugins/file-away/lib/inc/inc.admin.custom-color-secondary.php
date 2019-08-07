<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
$output .=  
'/* YOURCOLOR SECONDARY */
/* 	Add any custom list classes here to hook them into your color,
	adding a comma after each selector */
div[id^="ssfa-list-wrap"].ssfa-silk a.accent-yourcolor:hover:before,
div[id^="ssfa-list-wrap"].ssfa-minimal-list .accent-yourcolor div.ssfa-listitem:hover span.ssfa-topline,
div[id^="ssfa-list-wrap"].ssfa-minimal-list .accent-yourcolor div.ssfa-listitem:active span.ssfa-topline {
    background: rgba(#, #, #, 0.1);
	/* Set the RGB for your accent color, here with 10% transparency */
}
/* 	Add any custom list classes here to hook them into your color,
	adding a comma after each selector */
table[id^="ssfa-table"] tbody td a.ssfa-yourcolor:hover span.ssfa-filename,
table[id^="ssfa-table"] tbody td a.ssfa-yourcolor:active span.ssfa-filename,
div[id^="ssfa-list-wrap"].ssfa-silk a.accent-yourcolor:hover,
div[id^="ssfa-list-wrap"].ssfa-silk a.accent-yourcolor:active {
	color: #YOURCOLOR;
}	
/* END YOURCOLOR SECONDARY */
';