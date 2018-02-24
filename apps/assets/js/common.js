/**
	*@author Faisal Latada mac_@gxrg.org
	*@description Select record
	*@param {string} val id file
 */
function select_record(val)
{
    var temp_id = document.getElementById('temp_id');
    var frm = $('#record-'+val);
    var chk = document.getElementById('del'+val);
    if(chk.checked == true)
    {
        frm
        .css({
            backgroundColor:'#fb3',
            width:frm.width(), 
            height:frm.height()
        });

        temp_id.value += val+"-";
    }
    else
    {
        frm
        .css({
            backgroundColor:'',
            width:frm.width(), 
            height:frm.height()
        });
        temp_id.value = temp_id.value.replace(val+'-','');
    }	
}


/**
	*@author Faisal Latada mac_@gxrg.org
	*@description Delete record
	*@param {string} url action to delete record
	*@param {string} redirect after action completed
 */
function delete_records(url,redirect)
{
    var temp_id = document.getElementById('temp_id');
    var answer = confirm("Are you sure to delete this record ??");
    if(answer)
    {
        var exp = explode("-",temp_id.value);
		for(var i=0;i<(exp.length-1);i++)
        {
            var frm = $('#record-'+exp[i]);
            //e.preventDefault();
            frm.fadeOut(1000,function() {
                frm.remove();
            });
        }
		$.ajax({
			type: 'post',
			data: 'id='+$('#temp_id').val(),
			url: url + '/delete'
		});
        setTimeout("window.location=' "+ redirect +"'",1000);
    }
}

/**
	*@author Faisal Latada mac_@gxrg.org
	*@description Moving Records
	*@param {string} url action to move record
	*@param {string} redirect after action completed
 */
function move_records(url,redirect)
{
    var temp_id = document.getElementById('temp_id');
    var answer = confirm("Are you sure want to move these record into news ??");
    if(answer)
    {
        var exp = explode("-",temp_id.value);
		for(var i=0;i<(exp.length-1);i++)
        {
            var frm = $('#record-'+exp[i]);
            //e.preventDefault();
            frm.fadeOut(1000,function() {
                frm.remove();
            });
        }
		$.ajax({
			type: 'post',
			data: 'id='+$('#temp_id').val(),
			url: url + '/move'
		});
        setTimeout("window.location=' "+ redirect +"'",1000);
    }
}

/**
	*@author Faisal Latada mac_@gxrg.org
	*@description Check All current list record
	*@param {string} id curent list record
	*@param {string} checked whether checked or unchecked all current list record
 */
function checkedAll (id, checked) 
{	
    var el = document.getElementById(id);
    var temp_id = document.getElementById('temp_id');
    var frm = document.getElementById("primary_check");
    if(frm.checked == true) checked = true;
    else checked = false;
		
    for (var i = 0; i < el.elements.length; i++) 
    {
        el.elements[i].checked = checked;
	  
        var frm = $("#record-"+el.elements[i].value);
	  
        if(el.elements[i].type == "checkbox" && el.elements[i].value > 0)
        {
            if(checked == true)
            {
                frm
                .css({
                    backgroundColor:'#fb3',
                    width:frm.width(), 
                    height:frm.height()
                });
			     
                temp_id.value += el.elements[i].value+"-";
            }
            else
            {
                frm
                .css({
                    backgroundColor:'',
                    width:frm.width(), 
                    height:frm.height()
                });
			      
                temp_id.value = temp_id.value.replace(el.elements[i].value+'-','');
            }
        }
    }
}

/**
	*@author Faisal Latada mac_@gxrg.org
	*@description Get xml http object (AJAX)
	*@param {void} 
	*@return {object} object xml http request
 */
function GetXmlHttpObject()
{
    var xmlHttp=null;
    try
    {
        // Firefox, Opera 8.0+, Safari
        xmlHttp=new XMLHttpRequest();
    }
    catch (e)
    {
        // Internet Explorer
        try
        {
            xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch (e)
        {
            xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    }
    return xmlHttp;
}

/**
	*@author Faisal Latada mac_@gxrg.org
	*@description Change status to Publish / Unpublished
	*@param {string} id record
	*@param {string} url action 
 */
function change_publish(id,url)
{
    var xmlHttp;
    var frm = document.getElementById("pub"+id);
    xmlHttp=GetXmlHttpObject()
    if (xmlHttp==null)
    {
        alert ("Your browser does not support AJAX!");
        return;
    }
  	
    var uri=url+"/change_publish/"+id;
	
    xmlHttp.onreadystatechange=function()
    {
        if(xmlHttp.readyState==4)
        {
            frm.innerHTML=xmlHttp.responseText;
        }
    }
    xmlHttp.open("GET",uri,true);
    xmlHttp.send(null);
}

/**
	*@author Faisal Latada mac_@gxrg.org
	*@description Change status to new stuff
	*@param {string} id record
	*@param {string} url action 
 */
function change_stat(id,url)
{
    var xmlHttp;
    var frm = document.getElementById("stat"+id);
    xmlHttp=GetXmlHttpObject()
    if (xmlHttp==null)
    {
        alert ("Your browser does not support AJAX!");
        return;
    }
  	
    var uri=url+"/change_stat/"+id;
	
    xmlHttp.onreadystatechange=function()
    {
        if(xmlHttp.readyState==4)
        {
            frm.innerHTML=xmlHttp.responseText;
        }
    }
    xmlHttp.open("GET",uri,true);
    xmlHttp.send(null);
}


/**
	*@author Faisal Latada mac_@gxrg.org
	*@description Change sort to up / down
	*@param {int} current position
	*@param {int} id record
	*@param {int} id parent record
	*@param {string} direction to sort 
	*@param {string} url action to did the task 
 */
function change_sort(urut,id,id_parent,direction,url)
{
    var frm = $('#record-'+id)
    $.ajax({
        type: 'POST',
        data: 'id='+id+'&parent_id='+id_parent+'&urut='+urut+'&direction='+direction,
        url: url + '/change_sort/'
    },frm.fadeOut(1000,function() {
        frm.remove();
    }));
    setTimeout("window.location=' "+ url +" '",1000);
}


/**
	*@author Faisal Latada mac_@gxrg.org
	*@description changing schedule
	*@param {string} timestamp
	*@param {string} base url
	*@return {void}
	
 */
function change_schedule(stamp,locate,url,el) {
	obj   = $(el);
	dcls  = obj.attr('class');
	cls   = dcls == 'alert' ? 'alert-box' : 'alert';
	label ='Loading...';
	dfault = obj.html();
	
	if(label != dfault) {
		
		$.ajax({
			type: 'POST',
			data: 'stamp='+stamp+'&locate='+locate,
			url: url + '/change_sch/',
			beforeSend : function () {obj.html(label);},
			success : function (resp) {
				if(resp != 'false') {
					obj.html(resp);
					obj.removeClass(dcls).addClass(cls);
				}else {
					obj.html(dfault);
				}
			}
		});
	}else {
		alert('Please wait till the process completed. . .');
	}
}

/**
	*@author Faisal Latada mac_@gxrg.org
	*@description Explode string to an array
	*@param {string} delimeter of string
	*@param {int} limit of exploded
	*@return {array} an array exploded
	
 */
function explode( delimiter, string, limit )
{
    var emptyArray = {
        0: ''
    };
   
    // third argument is not required
    if ( arguments.length < 2
        || typeof arguments[0] == 'undefined'
        || typeof arguments[1] == 'undefined' )
        {
        return null;
    }
   
    if ( delimiter === ''
        || delimiter === false
        || delimiter === null )
        {
        return false;
    }
   
    if ( typeof delimiter == 'function'
        || typeof delimiter == 'object'
        || typeof string == 'function'
        || typeof string == 'object' )
        {
        return emptyArray;
    }
   
    if ( delimiter === true )
    {
        delimiter = '1';
    }
   
    if (!limit)
    {
        return string.toString().split(delimiter.toString());
    }
    else
    {
        // support for limit argument
        var splitted = string.toString().split(delimiter.toString());
        var partA = splitted.splice(0, limit - 1);
        var partB = splitted.join(delimiter.toString());
        partA.push(partB);
        return partA;
    }
}

var type = false;
/**
	*@author Faisal Latada mac_@gxrg.org
	*@description Crawling reference based on request
	*@param {string} path to action script
	*@param {string} value of reference request
	*@param {string} DOM id holder
	
 */
function crawl_ref(action,val,holder,type) {
	$.ajax({
		type: 'POST',
		data: 'id='+val+'&t='+type,
		success: function(data){
			if(data != '0')
				$("#"+holder).html(data);
			return false;
		},
		url: action,
		cache:false
	});
}

/**
	*@author Faisal Latada mac_@gxrg.org
	*@description Routine for clearing ref values
	
*/
function clear_ref() {
	$('#car_series').html('<option> --- Choice Series --- </option>');
	$('#car_types').html('<option> --- Choice Type --- </option>');
	/*$('#car').html('<option> --- Choice Type --- </option>'); */
}

/**
	*@author Faisal Latada mac_@gxrg.org
	*@description Routine for restore car form elements
	
*/
function toggling_form(form,button,label) {
	var labels = ($('#'+button).html() == label) ? 'Cancel' : label;
	$('#'+button).html(labels);
	$('#'+form).animate({
		opacity:'toggle',
		height:'toggle'
	});
}

/**
	*@author Faisal Latada mac_@gxrg.org
	*@description Routine for clearing form element
	
*/
function clear_form_elements(ele) {
    $(ele).find(':input').each(function() {
		//if(this.attr('class').substr(0,8) != 'no_clear'){ //class no_clear would be skiped. note : no clear class must be first called
			if(this.name.substr(0,4) != 'grup'){//old version, still used on auth grup, consider it dude
			  switch(this.type) {
					case 'password':
					case 'select-multiple':
					case 'select-one':
					case 'text':
					case 'file':
					case 'hidden':
					case 'textarea':
						 $(this).val('');
						 break;
					case 'checkbox':
					case 'radio':
						 this.checked = false;
				}
			}
		//}
    });
}



/**
	*@author Faisal Latada mac_@gxrg.org
	*@description function for creating Jquery Popup
	*@param idFrom string id for
	*@param width int width
	*@param height int height
	
*/
function jPopup(idForm,width,height){
	if (width==null){
		width = 250;
	}
	if(height==null){
		height= 300;
	}
		$( "#"+idForm ).dialog({
			autoOpen	: false,
			height	: height,
			width		: width,
			modal		: true,
			closeOnEscape : false,
			show		:"fade",
			hide		:"fade",
		});	
		$(  "#"+idForm ).dialog( "open" );
}

/**
	*@author Faisal Latada mac_@gxrg.org
	*@description Global Event and trigger 
*/
$(document).ready(function(){
    $(".plus-panel").click(function(){
        $(".plus-panel").hide();
        $(".min-panel").show();

        //$('#panel-form').fadeIn('slow');
        $('#panel-form').animate({
            opacity:'toggle',
            height:'toggle'
        });
		$('#panel-form').css('overflow','visible');
    });
    $(".min-panel").click(function(){
        $(".min-panel").hide();
        $(".plus-panel").show();

        //$('#panel-form').fadeOut('slow');
        $('#panel-form').animate({
            opacity:'toggle',
            height:'toggle'
        });
		$('#panel-form').css('overflow','visible');
    });
    
    // datepicker
    $(".datepicker_now").datepicker({
        dateFormat:'dd-mm-yy',
		changeMonth: true,
		changeYear: true,
		yearRange: '1950:2020'
    });
	if (typeof(availableTags) == 'undefined') {
		availableTags = '';
	}
	//$('#acomplete').autocomplete({
	//	source: availableTags,
	//	select: function(event, ui) {
	//		$("#acomplete").val(ui.item.val);
     //       $("#acomplete").attr('key',ui.item.key);
	//		$.ajax({
	//			type: 'POST',
	//			data: 'id='+$(this).attr('key'),
	//			success: function(data){
	//				if(data != '0') {
	//					$("#ref_user_cars").html(data);
	//				}
	//			},
	//			url: 'http://103.244.206.213/ford/index.php/admpage/wreserve/get_car',
	//			cache:false
	//		});
    //
     //   },
	//});
	
});



