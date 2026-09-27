/*=====REZA====*/

var SITE = SITE || {};
SITE.fileInputs = function() {
    var $this = jQuery(this),
    $val = $this.val(),
    valArray = $val.split('\\'),
    newVal = valArray[valArray.length-1],
    $button = $this.siblings('.button'),
    $fakeFile = $this.siblings('.file-holder');
    if(newVal !== '') {
        //$button.text('Photo Chosen');
        if($fakeFile.length === 0) {
            $button.after('<span class="file-holder">' + newVal + '</span>');
        } else {
            $fakeFile.text(newVal);
        }
    }
}

jQuery(document).ready(function($){
    $('.toplable .stapcont:last-child').css('padding','0');
	
    $('.selectGroup .customSelect:last-child').css('margin','0');
	
    //if( !$.browser.opera ){
    $('.customSelect select').each(function(){
        var title, cls = 'select-esa';
        title = ($('option:selected',this).length >0)? $('option:selected',this).text():$('option:eq(0)',this).text();
        //title = '';
        $(this)
        .addClass('transparent') //css({'opacity':0,'-khtml-appearance':'none'})
        .after('<span class="' + cls + '"><b><em>' + title + '</em></b></span>')
        .change(function(){
            val = ($('option:selected',this).length > 0)? $('option:selected',this).text():$('option',this).eq(0).text();
            $(this).next().find('em').text(val);
        })
        .hover(function(){
            $(this).next('span.'+cls).toggleClass(cls+'_hover')
            });
    });
    //}
	
    $('.switch').each(function(i,ob){
        $(ob).click(function(){
            var ckd = $('input',ob).is(':checked');
            $('input',ob).prop('checked',!ckd).closest(ob).toggleClass('swith-on',!ckd);
        }).toggleClass('swith-on',$('input',ob).is(':checked'));
    });
	
    //http://viget.com/inspire/custom-file-inputs-with-a-bit-of-jquery
    $('.file-wrapper input[type=file]').bind('change focus click', SITE.fileInputs);
	
    //Check box:
    $('input.chk, input[type=radio]').each(function(){
        $(this).prettyCheckable();
    });
	
    $('.chkgroup .chk[type="checkbox"]').change(function(e){
        if( this.id == 'chk-general' ){
            if( this.checked )
                $('.chk[type="checkbox"]',this.parentNode.parentNode).not(e.target).prettyCheckable('uncheck');
        }else{
            $('#chk-general:checked',this.parentNode.parentNode).each(function(){
                $(this).prettyCheckable('uncheck')
                });
        }
    });
	
	
    /*if($("nav.mainNav").length > 0){
		$("nav.mainNav ul li").hover(
			function(){
				$(this).children("ul").stop(true,true).show().parent('li').addClass('selected');
			},
			function(){
				$(this).children("ul").hide().parent('li').removeClass('selected');
			}
		);
		alignMenu();
		$(window).resize(function(){
			alignMenu();
		})
	};*/
	
    $('ul.menu').each(function(){
        $(this).find('li').each(function(){
            if($(this).find('ul').length > 0) 
                $(this).children('a').addClass('parent');
        });
    });
	
    if($("div.accordianOpen").length > 0){
        $("div.accordianOpen").first().show();
		
        heightFixed();
        $(window).resize(function(){
            heightFixed();
        });
		
        $('a.btnOpen').click(function() {
            if(!$(this).hasClass('btnClose')){
                $('a.btnOpen').removeClass('btnClose');
                $('div.accordianOpen').slideUp('');
                $(this).addClass('btnClose').next('div.accordianOpen').stop(true).slideDown();
            }
            return false;
        });
    };
	
    $('a.btn1').each(function(i){
        $(this).click(function(e) {
            $('.fixedHdr').addClass('not_fixed');
            e.preventDefault();
            $('.fxdFormCon form').eq(i).stop(true,true).slideToggle().siblings().css('display','none');
            $(this).toggleClass('active').siblings('a').removeClass('active');
            if(!$(this).hasClass('active')){
                $('.fixedHdr').removeClass('not_fixed');
            };
            $('body,html').animate({
                scrollTop:0
            },400);
        })
    });
	
    if($(".cal_table").length > 0){
        widthFixed();
        $(window).resize(function(){
            widthFixed();
        });
    };

    // input field
    $('input.txtBox1, .txtBox2').focus(function(){
        if(this.value==this.defaultValue) this.value='';
    }).blur(function(){
        if(this.value=='') this.value=this.defaultValue;
    });
	
    if($(".newsTicker").length > 0)
        $('#js-news').ticker();
	
    $("#AdmissionFormForm #photo").change(function() {
       readURL(this);

    });
});

jQuery(window).load(function() {
    jQuery('#slider').nivoSlider();
});

function heightFixed(){
    var contentHeight = jQuery('div.accordianOpen ul li')[0].offsetHeight;
    var totalHgt = jQuery("div.accordianOpen").css('height','0');
    jQuery("div.accordianOpen").css('height', (contentHeight * 5)+9);
};

function widthFixed(){
    var contentWidth = jQuery('.sidebarComn')[0].offsetWidth;
    var totalWidth = jQuery(".cal_table thead tr th").css('width','0');
    jQuery(".cal_table thead tr th").css('width', contentWidth /7);
};

function alignMenu(){
    alignMenuItems();
    $('.mainNav > ul > li > a').addClass('adjWdth');
    var winWdth = $(window).width();
    if( winWdth < 800){
        alignMenuItems();
    }else {
        $('.mainNav > ul > li > a').css({
            'width':'auto', 
            'padding':'0 30px'
        }).removeClass('adjWdth');
    }
}

function alignMenuItems(){
    var totEltWidth = 0;
    var menuWidth = jQuery('nav.mainNav ul')[0].offsetWidth;
    var availableWidth = 0;
    var space = 0;

    var elts = $('nav.mainNav > ul > li');
    var allWidth = {};
    elts.children('a.adjWdth').each(function(inx, elt) {
        jQuery(elt).css({
            'padding':'0', 
            'width':''
        });
        jQuery(elt).each(function(){
            totEltWidth += allWidth[inx] = elt.offsetWidth;
        //console.log({inx:inx,allWidth:allWidth[inx],totEltWidth:totEltWidth});
        });
    });
    
    availableWidth = menuWidth - totEltWidth;

    space = parseInt(availableWidth/(elts.length));
    
    elts.children('a.adjWdth').each(function(inx, elt) {
        jQuery(elt).css({
            width:((allWidth[inx]+space)+'px')
            });
    //console.log({inx:inx,allWidth:allWidth[inx],space:space,availableWidth:availableWidth,menuWidth:menuWidth,totEltWidth:totEltWidth});
    });
}

function readURL(input) {
    // if IE < 10 doesn't support FileReader
    if(!window.FileReader){
    // don't know how to proceed to assign src to image tag
    } else {
       
        if( input.files && input.files[0] ){
           
           
           
           var reader = new FileReader();
            reader.onload = function(e) { 
               // alert(e.target.result);
                $('#photoPreview').attr('src', e.target.result);
             //   alert('llo');    
        }
            reader.readAsDataURL(input.files[0]);
        }
    }
}
$(function() {
        $('#levId').change(function(e) {
            $("#dvloader").show();
            setTimeout(function() {
                $("#dvloader").hide()
                },2000);
            $('#secId').load('/students/level_sec_ajax/'+$(this).val());
        });
    });	
$(function() {
    $( "#datepicker" ).datepicker({
        dateFormat: 'yy-mm-dd'
    });
});
    
$(function() {
    $( "#datepicker1" ).datepicker({
        dateFormat: 'yy-mm-dd'
    });
});

function get_sid_by_roll(roll){
    $('#roll-loader').show();
    $.ajax({                   
        url: '/students/get_sid_by_roll/',
        cache: false,
        type: 'GET',
       data : {
            "roll" :  roll
        },
        dataType: 'HTML',
        success: function (data) {
              $('#roll-sid').html(data); 
              $('#roll-loader').hide();
        }
    });
    
}

$(function() {
    $( "#dept").change(function( event ) {
        $("#ajx-loading").show();
        var e = document.getElementById("dept");
        var strUser = e.options[e.selectedIndex].value;
        // alert(strUser);
        $.ajax({    
            url: '/student_results/dept_ajax' ,
            cache: false,
            type: 'GET',
            dataType: 'HTML',
            data : {
                "deptId": strUser
            },
            success: function (html) {
                $("#group").html(html);
                 $("#ajx-loading").hide();
            }
                
        });
       
    });
});


