<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
$output .= 
'/***** YOUR TABLE STYLE *****/
table[id^="ssfa-table"].ssfa-yourtablestyle > tbody > tr > td.ssfa-sortdate,
table[id^="ssfa-table"].ssfa-yourtablestyle > thead > tr > th.ssfa-sortdate {
	width: 140px;
}
table[id^="ssfa-table"].ssfa-yourtablestyle > tbody > tr > td.ssfa-sortsize,
table[id^="ssfa-table"].ssfa-yourtablestyle > thead > tr > th.ssfa-sortsize {
	width: 45px;
}
table[id^="ssfa-table"].ssfa-yourtablestyle > tbody > tr > td.ssfa-sorttype,
table[id^="ssfa-table"].ssfa-yourtablestyle > thead > tr > th.ssfa-sorttype {
	text-align: center; 
	width: 70px;
}
table[id^="ssfa-table"].ssfa-yourtablestyle {
	background: #FFFFFF;
	border: 0;
	border-bottom: 0;
	border-collapse: separate;
	-moz-border-radius: 0;
	-webkit-border-radius: 0;
	border-radius: 0;
	border-spacing: 0;
	color: #666666;
	font-family: 	"Lucida Sans Unicode", 
					"Lucida Grande", 
					"Droid Sans", 
					"Trebuchet MS", 
					sans-serif;
	font-size: 12px;
	line-height: 16px;
	margin: 0 0 0 0;
	-moz-transition: all .4s ease-in;
	-o-transition: all .4s ease-in;
	-webkit-transition: all .4s ease-in;
	transition: all .4s ease-in;  
}
table[id^="ssfa-table"].ssfa-yourtablestyle > thead {
	border: 0;
	margin: 0;
	padding: 0;
	vertical-align: baseline;
}
table[id^="ssfa-table"].ssfa-yourtablestyle > thead > tr > th {
	border-bottom: 0;
	border-left: 1px solid #F0F0F0;
	border-right: 0;
	border-top: 1px solid #CCCCCC;
	color: #777777;
	font-family: 	"Lucida Sans Unicode", 
					"Lucida Grande", 
					"Droid Sans", 
					"Open Sans", 
					"Trebuchet MS", 
					sans-serif;
	font-size: 12px;
	font-weight: 500;
	letter-spacing: 0.5px;
	padding: 10px;
	text-transform: uppercase;
}
table[id^="ssfa-table"].ssfa-yourtablestyle > thead > tr > th,
table[id^="ssfa-table"].ssfa-yourtablestyle > tbody > tr > td,
table[id^="ssfa-table"].ssfa-yourtablestyle > tfoot > tr > td {
	background-color: #FFFFFF;
	background-image: none;
	text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
	vertical-align: middle;    
}
table[id^="ssfa-table"].ssfa-yourtablestyle > tbody > tr:hover > td {
	background: #F9F9F9;
}
table[id^="ssfa-table"].ssfa-yourtablestyle > thead > tr > th.ssfa-yourtablestyle-first-column,
table[id^="ssfa-table"].ssfa-yourtablestyle > tbody > tr > td.ssfa-yourtablestyle-first-column {
	border-left: none;
}
table[id^="ssfa-table"].ssfa-yourtablestyle > tbody > tr > td {
	border-bottom: 0;
	border-left: 1px solid #F0F0F0;
	border-right: 0;
	border-top: 1px solid #F0F0F0;
	line-height: 14px;
	padding: 10px;
	vertical-align: middle;    
}
table[id^="ssfa-table"].ssfa-yourtablestyle > tbody > tr > td > a:hover,
table[id^="ssfa-table"].ssfa-yourtablestyle > tbody > tr > td > a:focus,
table[id^="ssfa-table"].ssfa-yourtablestyle > tbody > tr > td > a:active {
	color: #BD5A35; 	
}
table[id^="ssfa-table"].ssfa-yourtablestyle > tfoot > tr > td {
	background-image: none;
	border-bottom: 0;
	border-left: 0;  
	border-right: 0;  
	border-top: 1px solid #CCCCCC;
	box-shadow: none;
	padding: 10px;
	text-shadow: 0;
}
table[id^="ssfa-table"].ssfa-yourtablestyle > tfoot > tr > td {
	text-align: center; 
}
table[id^="ssfa-table"].ssfa-yourtablestyle > tfoot > tr:hover, 
table[id^="ssfa-table"].ssfa-yourtablestyle > tfoot > tr:hover td {
	background: transparent; 
}
table[id^="ssfa-table"].ssfa-yourtablestyle > tfoot > tr > td > div.ssfa-pagination {
	border: 0;
	padding: 0;
	margin: 0;
	text-align: center;
}
table[id^="ssfa-table"].ssfa-yourtablestyle > tfoot > tr > td > div.ssfa-pagination > ul {
	background-color: transparent;
	-moz-box-shadow: none;
	-o-box-shadow: none;
	-webkit-box-shadow: none;
	box-shadow: none;
	display: inline-block;
	margin: 0;
	padding: 0;
}
table[id^="ssfa-table"].ssfa-yourtablestyle > tfoot > tr > td > div.ssfa-pagination > ul > li {
	display: inline;
}
table[id^="ssfa-table"].ssfa-yourtablestyle > tfoot > tr > td > div.ssfa-pagination > ul > li > a,
table[id^="ssfa-table"].ssfa-yourtablestyle > tfoot > tr > td > div.ssfa-pagination > ul > li > span {
	border: 0;
	float: left;
	line-height: 15px;
	padding: 4px 12px;
	text-decoration: none;
}
table[id^="ssfa-table"].ssfa-yourtablestyle > tfoot > tr > td > div.ssfa-pagination > ul > li > a:hover,
table[id^="ssfa-table"].ssfa-yourtablestyle > tfoot > tr > td > div.ssfa-pagination > ul > li > a:focus {
	background: #EEEEEE;
	color: #BD5A35; 		  
}
table[id^="ssfa-table"].ssfa-yourtablestyle > tfoot > tr > td > div.ssfa-pagination > ul > li.active > a,
table[id^="ssfa-table"].ssfa-yourtablestyle > tfoot > tr > td > div.ssfa-pagination > ul > li.active > span {
	background: #EEEEEE;  
	color: #444444;
	cursor: default;
}
table[id^="ssfa-table"].ssfa-yourtablestyle > tfoot > tr > td > div.ssfa-pagination > ul > .disabled > span,
table[id^="ssfa-table"].ssfa-yourtablestyle > tfoot > tr > td > div.ssfa-pagination > ul > .disabled > a,
table[id^="ssfa-table"].ssfa-yourtablestyle > tfoot > tr > td > div.ssfa-pagination > ul > .disabled > a:hover,
table[id^="ssfa-table"].ssfa-yourtablestyle > tfoot > tr > td > div.ssfa-pagination > ul > .disabled > a:focus {
	background-color: transparent; 
	color: #D8D8D8;
	cursor: default;
}
table[id^="ssfa-table"].ssfa-yourtablestyle > tfoot > tr > td > div.ssfa-pagination.ssfa-pagination-centered,
table[id^="ssfa-table"].ssfa-yourtablestyle > tfoot > tr > td > div.ssfa-pagination.ssfa-pagination-right {
	text-align: center;
}
/***** END YOUR TABLE STYLE *****/
';