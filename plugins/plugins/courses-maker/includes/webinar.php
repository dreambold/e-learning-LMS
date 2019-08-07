<style>

</style>

<script>

function getObj(objID)

{

    if (document.getElementById) {return document.getElementById(objID);}

    else if (document.all) {return document.all[objID];}

    else if (document.layers) {return document.layers[objID];}

}



function checkClick(e) {

	e?evt=e:evt=event;

	CSE=evt.target?evt.target:evt.srcElement;

	if (CSE.tagName!='SPAN')

	if (getObj('fc'))

		if (!isChild(CSE,getObj('fc')))

			getObj('fc').style.display='none';

}



function isChild(s,d) {

	while(s) {

		if (s==d)

			return true;

		s=s.parentNode;

	}

	return false;

}



function Left(obj)

{

	var curleft = 0;

	if (obj.offsetParent)

	{

		while (obj.offsetParent)

		{

			curleft += obj.offsetLeft

			obj = obj.offsetParent/4;

			console.log(obj);

		}

	}

	else if (obj.x)

		curleft += obj.x;

	return curleft;

}



function Top(obj)

{

	var curtop = 0;

	if (obj.offsetParent)

	{

		while (obj.offsetParent)

		{

			curtop += obj.offsetTop-50

			obj = obj.offsetParent;

		}

	}

	else if (obj.y)

		curtop += obj.y;

	return curtop;

}



// Calendar script

var now = new Date;

var sccd=now.getDate();

var sccm=now.getMonth();

var sccy=now.getFullYear();

var ccm=now.getMonth();

var ccy=now.getFullYear();



// For current selected date

var selectedd, selectedm, selectedy;



document.write('<table id="fc" style="position:absolute;border-collapse:collapse;background:#FFFFFF;border:1px solid #FFD088;display:none;width:265px;-moz-user-select:none;-khtml-user-select:none;user-select:none;" cellpadding="2">');

document.write('<tr style="font:bold 19px Arial" onselectstart="return false"><td style="cursor:pointer;font-size:15px" onclick="upmonth(-1)">&laquo;</td><td colspan="5" id="mns" align="center"></td><td align="right" style="cursor:pointer;font-size:15px" onclick="upmonth(1)">&raquo;</td></tr>');

document.write('<tr style="background:#F7D900;font:18px Arial;color:#FFFFFF"><td align=center>Mo</td><td align=center>Di</td><td align=center>Mi</td><td align=center>Do</td><td align=center>Fr</td><td align=center>Sa</td><td align=center>So</td></tr>');

for(var kk=1;kk<=6;kk++) {

	document.write('<tr>');

	for(var tt=1;tt<=7;tt++) {

		num=7 * (kk-1) - (-tt);

		document.write('<td id="cv' + num + '" style="width:26px;height:26px">&nbsp;</td>');

	}

	document.write('</tr>');

}

document.write('<tr><td colspan="7" align="center" style="cursor:pointer;font:19px Arial;background:#F7D900" onclick="today()">Heute: '+addnull(sccd,sccm+1,sccy)+'</td></tr>');

document.write('</table>');



document.all?document.attachEvent('onclick',checkClick):document.addEventListener('click',checkClick,false);









var updobj;

function lcs(ielem) {

	updobj=ielem;

	getObj('fc').style.left=Left(ielem)+'px';

	getObj('fc').style.top=Top(ielem)+ielem.offsetHeight+'px';

	getObj('fc').style.display='';



	// First check date is valid

	curdt=ielem.value;

	curdtarr=curdt.split('-');

	isdt=true;

	for(var k=0;k<curdtarr.length;k++) {

		if (isNaN(curdtarr[k]))

			isdt=false;

	}

	if (isdt&(curdtarr.length==3)) {

		ccm=curdtarr[1]-1;

		ccy=curdtarr[2];



		selectedd=parseInt ( curdtarr[0], 10 );

		selectedm=parseInt ( curdtarr[1]-1, 10 );

		selectedy=parseInt ( curdtarr[2], 10 );



		prepcalendar(curdtarr[0],curdtarr[1]-1,curdtarr[2]);

	}



}



function evtTgt(e){

	var el;

	if(e.target)el=e.target;

	else if(e.srcElement)el=e.srcElement;

	if(el.nodeType==3)el=el.parentNode; // defeat Safari bug

	return el;

}

function EvtObj(e){if(!e)e=window.event;return e;}

function cs_over(e) {

	evtTgt(EvtObj(e)).style.background='#FFEBCC';

}

function cs_out(e) {

	evtTgt(EvtObj(e)).style.background='#FFFFFF';

}

function cs_click(e) {

	updobj.value=calvalarr[evtTgt(EvtObj(e)).id.substring(2,evtTgt(EvtObj(e)).id.length)];

	getObj('fc').style.display='none';

}



var mn=new Array('Januar ','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember');

var mnn=new Array('31','28','31','30','31','30','31','31','30','31','30','31');

var mnl=new Array('31','29','31','30','31','30','31','31','30','31','30','31');

var calvalarr=new Array(42);



function f_cps(obj) {

	obj.style.background='#FFFFFF';

	obj.style.font='16px Arial';

	obj.style.color='#333333';

	obj.style.textAlign='center';

	obj.style.textDecoration='none';

	obj.style.border='1px solid #FFD088';//'1px solid #606060';

	obj.style.cursor='pointer';

}



function f_cpps(obj) {

	obj.style.background='#C4D3EA';

	obj.style.font='16px Arial';

	obj.style.color='#F7D900';

	obj.style.textAlign='center';

	obj.style.textDecoration='line-through';

	obj.style.border='1px solid #6487AE';

	obj.style.cursor='default';

}



function f_hds(obj) {

	obj.style.background='#FFF799';

	obj.style.font='bold 16px Arial';

	obj.style.color='#333333';

	obj.style.textAlign='center';

	obj.style.border='1px solid #6487AE';

	obj.style.cursor='pointer';

}



// day selected

function prepcalendar(hd,cm,cy) {

	now=new Date();

	sd=now.getDate();

	td=new Date();

	td.setDate(1);

	td.setFullYear(cy);

	td.setMonth(cm);

	cd=td.getDay();

	if (cd==0)cd=6; else cd--;

	getObj('mns').innerHTML=mn[cm]+'&nbsp;<span style="cursor:pointer" onclick="upmonth(-12)">&lt;</span>'+cy+'<span style="cursor:pointer" onclick="upmonth(12)">&gt;</span>';

	marr=((cy%4)==0)?mnl:mnn;

	for(var d=1;d<=42;d++) {

		cv=getObj('cv'+parseInt(d));

		f_cps(cv);

		if ((d >= (cd -(-1)))&&(d<=cd-(-marr[cm]))) {

			dip=((d-cd < sd)&&(cm==sccm)&&(cy==sccy));

			htd=((hd!='')&&(d-cd==hd));



			cv.onmouseover=cs_over;

			cv.onmouseout=cs_out;

			cv.onclick=cs_click;



			// if today

			if (sccm == cm && sccd == (d-cd) && sccy == cy)

				cv.style.color='#F7D900';



			// if selected date

			if (cm == selectedm && cy == selectedy && selectedd == (d-cd) )

			{

				cv.style.background='#FFEBCC';

				//cv.style.color='#e0d0c0';

				//cv.style.fontSize='1.1em';

				//cv.style.fontStyle='italic';

				//cv.style.fontWeight='bold';



				// when use style.background

				cv.onmouseout=null;

			}



			cv.innerHTML=d-cd;



			calvalarr[d]=addnull(d-cd,cm-(-1),cy);

		}

		else {

			cv.innerHTML='&nbsp;';

			cv.onmouseover=null;

			cv.onmouseout=null;

			cv.onclick=null;

			cv.style.cursor='default';

			}

	}

}



prepcalendar('',ccm,ccy);



function upmonth(s)

{

	marr=((ccy%4)==0)?mnl:mnn;



	ccm+=s;

	if (ccm>=12)

	{

		ccm-=12;

		ccy++;

	}

	else if(ccm<0)

	{

		ccm+=12;

		ccy--;

	}

	prepcalendar('',ccm,ccy);

}



function today() {

	updobj.value=addnull(now.getDate(),now.getMonth()+1,now.getFullYear());

	getObj('fc').style.display='none';

	prepcalendar('',sccm,sccy);

}



function addnull(d,m,y)

{

	var d0='',m0='';

	if (d<10)d0='0';

	if (m<10)m0='0';



	return ''+d0+d+'-'+m0+m+'-'+y;

}

</script>

<form method="POST" action="<?php echo plugins_url('save-webinar.php', __FILE__); ?>" style="display: flex; flex-direction: column; min-width: 100%;">
    <div class="add-header">
        <h1><input id="post_title" name="post_title" title="Title" type="text" autocomplete="on"  placeholder="Title"></h1>
    </div>
    <input type="hidden" id="id" name="id" value="<?php // echo get_the_ID();?>">
    <input type="hidden" id="attachment_id" name="attachment_id" value="<?php if (!is_wp_error($attachment_id)) {
        echo $attachment_id;
    } ?>">
    <input type="hidden" id="user_id" name="user_id" value="<?php echo get_current_user_id(); ?>">
    <input type="hidden" id="post_date" name="post_date" value="<?php echo current_time(" Y-m-d H:i:s "); ?>">
    <input type="hidden" id="post_date_gmt" name="post_date_gmt" value="<?php echo get_gmt_from_date(current_time(" Y-m-d H:i:s ")); ?>">
    <input type="hidden" id="post_status" name="post_status" value="publish">
    <input type="hidden" id="comment_status" name="comment_status" value="closed">
    <input type="hidden" id="ping_status" name="ping_status" value="closed">
    <input type="hidden" id="post_password" name="post_password" value="">
    <input type="hidden" id="modified_date" name="modified_date" value="<?php echo current_time(" Y-m-d H:i:s "); ?>">
    <input type="hidden" id="modified_date_gmt" name="modified_date_gmt" value="<?php echo get_gmt_from_date(current_time(" Y-m-d H:i:s ")); ?>">
    <input type="hidden" id="post_parent" name="post_parent" value="">
    <input type="hidden" id="site_url" name="site_url" value="<?php echo get_site_url() ?>">
    <input type="hidden" id="menu_order" name="menu_order" value="0">
    <input type="hidden" id="post_type" name="post_type" value="webinars">
    <input type="hidden" id="comment_count" name="comment_count" value="0">
    <div id="primary" class="content-area">

            <h3>Content</h3>
            <p>Please inser webinare video chanel in this format "[clickmeeting lang=”en”]https://yourlogin.clickmeeting.com/webinar-name[/clickmeeting]". For creatin webinars you must be registrated <a href="https://clickmeeting.com">THERE</a></p>
            <?php
            $args = array(
            'wpautop' => 1, 
            'media_buttons' => 1, 
            'textarea_rows' => 10, 
            'tabindex' => 0, 
            'editor_css' => '', 
            'editor_class' => '', 
            'teeny' => 0, 
            'dfw' => 0, 
            'tinymce' => 1, 
            'quicktags' => 0,
            'drag_drop_upload' => true);
            wp_editor('', 'post_content', $args);
            ?>
        <div class="inside">
            <div style="display: flex;">
            <div>
            <label for="webinar_date"><br><b>Webinare Start Date: </b></label>
            <input autocomplete="off" type="text" id="webinar_date" name="webinar_date" placeholder="Select webinar start date" value="" onfocus="this.select();lcs(this)"onclick="event.cancelBubble=true;this.select();lcs(this)" required>
            <select name="webinar_start_hour" id="webinar_start_hour" required>
                <option disabled selected>Select a time</option>
                <option value="08:00">08:00</option>
                <option value="08:30">08:30</option>
                <option value="09:00">09:00</option>
                <option value="09:30">09:30</option>
                <option value="10:00">10:00</option>
                <option value="10:30">10:30</option>
                <option value="11:00">11:00</option>
                <option value="11:30">11:30</option>
                <option value="12:30">12:30</option>
                <option value="13:30">13:30</option>
                <option value="14:00">14:00</option>
                <option value="14:30">14:30</option>
                <option value="15:00">15:00</option>
                <option value="15:30">15:30</option>
                <option value="16:00">16:00</option>
                <option value="16:30">16:30</option>
                <option value="17:00">17:00</option>
                <option value="17:30">17:30</option>
                <option value="18:00">18:00</option>
                <option value="18:30">18:30</option>
                <option value="19:00">19:00</option>
                <option value="19:30">19:30</option>
                <option value="20:00">20:00</option>
                <option value="20:30">20:30</option>
                <option value="21:00">21:00</option>
                <option value="21:30">21:30</option>
                <option value="22:00">22:00</option>
            </select>
            </div>
            <div>
            <label for="webinar_date_end"><br><b>Webinare End Date: </b></label>
            <input autocomplete="off" type="text" id="webinar_date_end" name="webinar_date_end" placeholder="Select webinar end date" value="" onfocus="this.select();lcs(this)"onclick="event.cancelBubble=true;this.select();lcs(this)" required>
            <select name="webinar_end_hour" id="webinar_end_hour" required>
                <option disabled selected>Select a time</option>
                <option value="08:00">08:00</option>
                <option value="08:30">08:30</option>
                <option value="09:00">09:00</option>
                <option value="09:30">09:30</option>
                <option value="10:00">10:00</option>
                <option value="10:30">10:30</option>
                <option value="11:00">11:00</option>
                <option value="11:30">11:30</option>
                <option value="12:30">12:30</option>
                <option value="13:30">13:30</option>
                <option value="14:00">14:00</option>
                <option value="14:30">14:30</option>
                <option value="15:00">15:00</option>
                <option value="15:30">15:30</option>
                <option value="16:00">16:00</option>
                <option value="16:30">16:30</option>
                <option value="17:00">17:00</option>
                <option value="17:30">17:30</option>
                <option value="18:00">18:00</option>
                <option value="18:30">18:30</option>
                <option value="19:00">19:00</option>
                <option value="19:30">19:30</option>
                <option value="20:00">20:00</option>
                <option value="20:30">20:30</option>
                <option value="21:00">21:00</option>
                <option value="21:30">21:30</option>
                <option value="22:00">22:00</option>
            </select>            
        </div>
            <div>
            <label for="free_places"><br><b>Vacancies: </b></label>
            <input type="text" id="free_places" name="free_places" placeholder="Enter the number of vacancies" value="">
            </div>
            </div>            
        </div>

    </div>

    <div>
        <input class="button" type="submit" value="Save" />
    </div>

</form>

