var slide_trap = null;
var dl_first = null;
var dl_end = null;
var list = null;

var stockData = [];

var monthToday = moment().format('MM');
var monthToday2 = moment().format('MM');
var yearToday = moment().format('YYYY');
var loading = true;
var showCustomPicker = false;
var year_active = 1;

var date_slider = null;

var currencyType = localStorage.getItem("currency_type");

var rangePicker_start = moment().startOf('year').format( 'DD/MM/YYYY' );
var rangePicker_end = moment().format( 'DD/MM/YYYY' );

window.base_url = window.location.origin + '/app/';

function getFirstEndDate( firstMonth, lastMonth ){
	firstMonth = moment( firstMonth + " " + yearToday,'MM YYYY').format('YYYY-MM-DD');
	lastMonth = moment( lastMonth + " " + yearToday,'MM YYYY').format('YYYY-MM-DD');

	var date1 = new Date(firstMonth);
	var date2 = new Date(lastMonth);
	var y1 = date1.getFullYear();
	var m1 = date1.getMonth();
	var y2 = date2.getFullYear();
	var m2 = date2.getMonth();
	var firstDay = new Date(y1, m1, 1);
	var lastDay = new Date(y2, m2 + 1, 0);

	firstDay = moment(firstDay).format('YYYY-MM-DD');
	lastDay = moment(lastDay).format('YYYY-MM-DD');

	dl_first = firstDay;
	dl_end = lastDay;

	// console.log(firstDay);
	// console.log(lastDay);

	return {
		start: firstDay,
		end: lastDay,
	}
}

function convertArrayOfObjectsToCSV(args) {  
    var result, ctr, keys, columnDelimiter, lineDelimiter, data;

    data = args.data || null;
    if (data == null || !data.length) {
        return null;
    }

    columnDelimiter = args.columnDelimiter || ',';
    lineDelimiter = args.lineDelimiter || '\n';

    keys = Object.keys(data[0]);

    result = '';
    result += keys.join(columnDelimiter);
    result += lineDelimiter;

    data.forEach(function(item) {
        ctr = 0;
        keys.forEach(function(key) {
            if (ctr > 0) result += columnDelimiter;

            result += item[key];
            ctr++;
        });
        result += lineDelimiter;
    });

    return result;
}

function downloadCSV(args) {  
    var data, filename, link;
    var csv = convertArrayOfObjectsToCSV({
        data: stockData
    });
    if (csv == null) return;

    filename = args.filename || 'export.csv';

    if (!csv.match(/^data:text\/csv/i)) {
        csv = 'data:text/csv;charset=utf-8,' + csv;
    }
    data = encodeURI(csv);

    link = document.createElement('a');
    link.setAttribute('href', data);
    link.setAttribute('download', filename);
    link.click();
}

function getTransactions( data ){

	$( '.trans-history-tbl tbody' ).html('<tr>' +
							'<td colspan="7" class="text-center">' +
								'<h5>Loading...</h5>' +
							'</td>' +
						'</tr>');

	$.ajax({
      url: window.base_url+'clinic/transaction_lists',
      type: 'post',
      data:data
  })
  .done(function(data) {
  		console.log(data);
  		// var trans = data.data.transactions;

  		$( '.trans-history-tbl tbody' ).html('');
  		stockData = [];

  		$('.total-trans-num').text(data.data.total_transactions);
  		$('.medni-wallet-num').text(data.data.mednefits_wallet);
  		$('.currencyType').text(data.data.currency_type);

      if( data.data.transactions.length > 0 ){
      
	      list = data.data.transactions;
	      // console.log(list);

	      for( var i = 0; i < list.length; i++  ){
	      	// console.log( list );
	      	stockData.push({
	      		'DATE' : (list[i].date_of_transaction).replace(',',''),
	      		'TRANSACTION_ID' : list[i].transaction_id,
	      		'NAME' : list[i].user_name,
	      		// 'NRIC' : list[i].NRIC,
	      		'SERVICES' : list[i].procedure_name,
	      		'MEDNEFITS FEE' : list[i].currency_type.toUpperCase() + ' ' + (list[i].mednefits_fee).replace(',',''),
	      		'MEDNEFITS CREDIT' : list[i].currency_type.toUpperCase() + ' ' + (list[i].mednefits_credits).replace(',',''),
	      		'CASH' : list[i].currency_type.toUpperCase() + ' ' + (list[i].cash).replace(',',''),
	      		'STATUS' : list[i].transaction_status,
	      	});

	      	if(list[i].deleted == true) {
		      	if( list[i].currency_type == "myr" ){
		      		if(list[i].deleted == true) {
		      			$( '.trans-history-tbl tbody' ).append('<tr>' +
									'<td>' + list[i].date_of_transaction + '</td>' +
									'<td>' +
										list[i].transaction_id +
										'<br />' +
										'<label class="label label-success label-custom" >' + list[i].transaction_status + '</label>' +
									'</td>'	+
									'<td>' + list[i].user_name + '</td>' + 
									// '<td>' + list[i].NRIC + '</td>' +
									'<td>' + list[i].procedure_name + '</td>' +
									'<td style="text-align: center"><span style="text-transform: uppercase">' + list[i].currency_type + '</span> ' + list[i].mednefits_fee + '</td>' + 
									'<td style="text-align: center"><span style="text-transform: uppercase">' + list[i].currency_type + '</span> ' + list[i].mednefits_credits + '</td>' + 
									'<td style="text-align: center"><span style="text-transform: uppercase">' + list[i].currency_type + '</span> ' + list[i].cash + '</td>' + 
								'</tr>');
		      		} else {
		      			$( '.trans-history-tbl tbody' ).append('<tr>' +
									'<td>' + list[i].date_of_transaction + '</td>' +
									'<td>' +
										list[i].transaction_id +
									'</td>'	+
									'<td>' + list[i].user_name + '</td>' + 
									// '<td>' + list[i].NRIC + '</td>' +
									'<td>' + list[i].procedure_name + '</td>' +
									'<td style="text-align: center"><span style="text-transform: uppercase">' + list[i].currency_type + '</span> ' + list[i].mednefits_fee + '/td>' + 
									'<td style="text-align: center"><span style="text-transform: uppercase">' + list[i].currency_type + '</span> ' + list[i].mednefits_credits + '</td>' + 
									'<td style="text-align: center"><span style="text-transform: uppercase">' + list[i].currency_type + '</span> ' + list[i].cash + '</td>' + 
								'</tr>');
		      		}
		      	}else{
		      		if(list[i].deleted == true) {
		      			$( '.trans-history-tbl tbody' ).append('<tr>' +
									'<td>' + list[i].date_of_transaction + '</td>' +
									'<td>' +
										list[i].transaction_id +
										'<br />' +
										'<label class="label label-success label-custom" >' + list[i].transaction_status + '</label>' +
									'</td>'	+
									'<td>' + list[i].user_name + '</td>' + 
									// '<td>' + list[i].NRIC + '</td>' +
									'<td>' + list[i].procedure_name + '</td>' +
									'<td><span style="text-transform: uppercase">' + list[i].currency_type + '</span> ' + list[i].mednefits_fee + '</td>' + 
									'<td><span style="text-transform: uppercase">' + list[i].currency_type + '</span> ' + list[i].mednefits_credits + '</td>' +
									'<td><span style="text-transform: uppercase">' + list[i].currency_type + '</span> ' + list[i].cash + '</td>' +
								'</tr>');
		      		} else {
		      			$( '.trans-history-tbl tbody' ).append('<tr>' +
									'<td>' + list[i].date_of_transaction + '</td>' +
									'<td>' +
										list[i].transaction_id +
									'</td>'	+
									'<td>' + list[i].user_name + '</td>' + 
									// '<td>' + list[i].NRIC + '</td>' +
									'<td>' + list[i].procedure_name + '</td>' +
									'<td><span style="text-transform: uppercase">' + list[i].currency_type + '</span> ' + list[i].mednefits_fee + '</td>' + 
									'<td><span style="text-transform: uppercase">' + list[i].currency_type + '</span> ' + list[i].mednefits_credits + '</td>' +
									'<td><span style="text-transform: uppercase">' + list[i].currency_type + '</span> ' + list[i].cash + '</td>' +
								'</tr>');
		      		}
		      		
		      	}
	      	} else {
	      		if( list[i].currency_type == "myr" ){
		      		$( '.trans-history-tbl tbody' ).append('<tr>' +
									'<td>' + list[i].date_of_transaction + '</td>' +
									'<td>' +
										list[i].transaction_id +
									'</td>'	+
									'<td>' + list[i].user_name + '</td>' + 
									// '<td>' + list[i].NRIC + '</td>' +
									'<td>' + list[i].procedure_name + '</td>' +
									'<td><span style="text-transform: uppercase">' + list[i].currency_type + '</span> ' + list[i].mednefits_fee + '</td>' + 
									'<td><span style="text-transform: uppercase">' + list[i].currency_type + '</span> ' + list[i].mednefits_credits + '</td>' + 
									'<td><span style="text-transform: uppercase">' + list[i].currency_type + '</span> ' + list[i].cash + '</td>' + 
								'</tr>');
		      	}else{
		      		$( '.trans-history-tbl tbody' ).append('<tr>' +
									'<td>' + list[i].date_of_transaction + '</td>' +
									'<td>' +
										list[i].transaction_id +
									'</td>'	+
									'<td>' + list[i].user_name + '</td>' + 
									// '<td>' + list[i].NRIC + '</td>' +
									'<td>' + list[i].procedure_name + '</td>' +
									'<td><span style="text-transform: uppercase">' + list[i].currency_type + '</span> ' + list[i].mednefits_fee + '</td>' + 
									'<td><span style="text-transform: uppercase">' + list[i].currency_type + '</span> ' + list[i].mednefits_credits + '</td>' +
									'<td><span style="text-transform: uppercase">' + list[i].currency_type + '</span> ' + list[i].cash + '</td>' +
								'</tr>');
		      	}
	      	}
	      	setHeight();
	      }
      }else{
      	$( '.trans-history-tbl tbody' ).html('<tr>' +
							'<td colspan="7" class="text-center">' +
								'<h5>No Data</h5>' +
							'</td>' +
						'</tr>');
      }
      // console.log( stockData );
  });
}

function downloadPDF() {

	// $( '.trans-history-tbl tbody' ).css({'overflow-y':'visible', 'height':'auto'});

	var file = document.getElementById('invoice-print-dl');
  form = $('#invoice-print-dl'),
	  cache_width = form.width(),
	  pdf_name = 'Transaction History(' + moment(dl_first).format('MMMDDYY') + ' - ' + moment(dl_end).format('MMMDDYY') + ' )',
	  a4  = [ 615, 841.89];  

	  getCanvas().then(function(canvas){
	  	console.log(canvas);
	  	var imgWidth = 615;
		  var pageHeight = 841.89;
		 //  var imgWidth = 210;
			// var pageHeight = 295;
		  // var imgHeight = 690;
		  var imgHeight = canvas.height * imgWidth / canvas.width;;
			var heightLeft = imgHeight;
			var position = 0;

	    var img = canvas.toDataURL("image/png");
	    var doc = new jsPDF('portrait','px', 'a4'); 

		    var width1 = doc.internal.pageSize.width;    
				var height1 = doc.internal.pageSize.height;

				doc.margin = 1;
        doc.addImage(img, 'jpeg', -1, position);
        heightLeft -= pageHeight;

        while (heightLeft >= 0) {
         console.log(heightLeft);
				 position = heightLeft - imgHeight;
				 console.log(position);
				 doc.addPage();
				 doc.addImage(img, 'jpeg', -1, position);
				 heightLeft -= pageHeight;
				}

				window.open(doc.output('bloburl'), '_blank');

        // doc.save(pdf_name + '.pdf');

        form.width(cache_width);
	  });

};

// create canvas object

function getCanvas(){
  form.width((a4[0]*1.33333) -80).css('max-width','none');
  return html2canvas(form,{
      imageTimeout:2000,
      removeContainer:true,
      allowTaint: false,
      useCORS: true
    }); 
}

function searchTable(data) {
	console.log( data );
	if( data.search.length > 0 ){
		$( '.trans-history-tbl tbody' ).html('<tr>' +
						'<td colspan="7" class="text-center">' +
							'<h5>Loading...</h5>' +
						'</td>' +
					'</tr>');
	}
	

	$.ajax({
      url: window.base_url+'clinic/search_transaction_lists',
      type: 'post',
      data:data
  })
  .done(function(data) {
  		console.log(data);
  		$( '.trans-history-tbl tbody' ).html('');

  		if( data.data.transactions.length > 0 ){

  			var list = data.data.transactions;

	      for( var i = 0; i < list.length; i++  ){
	      	if( list[i].currency_type == "myr" ){
	      		$( '.trans-history-tbl tbody' ).append('<tr>' +
							'<td>' + list[i].date_of_transaction + '</td>' +
							'<td>' +
								list[i].transaction_id +
								'<br />' +
								'<label class="label label-success label-custom" >' + list[i].transaction_status + '</label>' +
							'</td>'	+
							'<td>' + list[i].user_name + '</td>' + 
							// '<td>' + list[i].NRIC + '</td>' +
							'<td>' + list[i].procedure_name + '</td>' +
							'<td style="text-align: center"><span style="text-transform: uppercase">' + list[i].currency_type + '</span> ' + list[i].mednefits_fee + '<br><span>(RM' + (list[i].mednefits_fee * list[i].currency_amount).toFixed(2) + ')</span></td>' + 
							'<td style="text-align: center"><span style="text-transform: uppercase">' + list[i].currency_type + '</span> ' + list[i].mednefits_credits + '<br><span>(RM' + (list[i].mednefits_credits * list[i].currency_amount).toFixed(2) + ')</span></td>' + 
							'<td style="text-align: center"><span style="text-transform: uppercase">' + list[i].currency_type + '</span> ' + list[i].cash + '<br><span>(RM' + (list[i].cash * list[i].currency_amount).toFixed(2) + ')</span></td>' + 
						'</tr>');
	      	}else{
	      		$( '.trans-history-tbl tbody' ).append('<tr>' + 
							'<td>' + list[i].date_of_transaction + '</td>' +
							'<td>' +
								list[i].transaction_id +
								'<br />' +
								'<label class="label label-success label-custom" >' + list[i].transaction_status + '</label>' +
							'</td>'	+
							'<td>' + list[i].user_name + '</td>' +
							// '<td>' + list[i].NRIC + '</td>' +
							'<td>' + list[i].procedure_name + '</td>' +
							'<td><span style="text-transform: uppercase">' + list[i].currency_type + '</span> ' + list[i].mednefits_fee + '</td>' +
							'<td><span style="text-transform: uppercase">' + list[i].currency_type + '</span> ' + list[i].mednefits_credits + '</td>' +
							'<td><span style="text-transform: uppercase">' + list[i].currency_type + '</span> ' + list[i].cash + '</td>' +
						'</tr>');
	      	}

	      	setHeight();
	      }
	      
  		}else{
      	$( '.trans-history-tbl tbody' ).html('<tr>' +
							'<td colspan="7" class="text-center">' +
								'<h5>No Data</h5>' +
							'</td>' +
						'</tr>');
      }
  });
}

function setHeight(){
	var page_height = $('#payments-detail-wrapper').height()+52;
	var win_height = $(window).height();
	if (page_height > win_height){

	  $("#setting-navigation").height($('#payments-detail-wrapper').height()+52);
	  $("#payments-side-list").height($('#payments-detail-wrapper').height()+52);
	}
	else{

	  $("#setting-navigation").height($(window).height()-52);
	  $("#payments-side-list").height($(window).height()-52);
	}
}

function showCustomDate( num ){
	year_active = num;
	$(".activity-date-header .year-selector a").removeClass('active');
	$(".activity-date-header .year-selector:nth-child(" + (num+1) + ") a").addClass('active');

	$( '.showCustomPickerTrue' ).hide();
	$( '.showCustomPickerFalse' ).fadeIn();
	

	setTimeout(function() {
		$('.btn-custom-start').daterangepicker({
			autoUpdateInput : true,
			autoApply : true,
			singleDatePicker: true,
			startDate : moment( rangePicker_start, 'DD/MM/YYYY' ).format( 'MM/DD/YYYY' ),
		}, function(start, end, label) {

		  rangePicker_start = moment( start ).format( 'DD/MM/YYYY' );
			$("#rangePicker_start").text( rangePicker_start );

			$('.btn-custom-end').data('daterangepicker').setMinDate( start );

			if( rangePicker_end && ( rangePicker_end > rangePicker_start ) ){
				var text = $( ".search-table" ).val();
			  var activity_search = {
			  	start: moment(rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD'),
					end: moment(rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD'),
			  };
			  // console.log(activity_search);

				if( text.length > 2 ){
					activity_search.search = text;
					searchTable( activity_search );
				}else{
					getTransactions( activity_search );
				}
			}else{
				rangePicker_end = moment( start ).format( 'DD/MM/YYYY' );
				$("#rangePicker_end").text( rangePicker_end );
			}
		});

		$('.btn-custom-end').daterangepicker({
			autoUpdateInput : true,
			autoApply : true,
			singleDatePicker: true,
			startDate : moment( rangePicker_end, 'DD/MM/YYYY' ).format( 'MM/DD/YYYY' ),
		}, function(start, end, label) {
		  
		  rangePicker_end = moment( end ).format( 'DD/MM/YYYY' );
			$("#rangePicker_end").text( rangePicker_end );

			var text = $( ".search-table" ).val();
		  var activity_search = {
		  	start: moment(rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD'),
				end: moment(rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD'),
		  };
		  // console.log(activity_search);

			if( text.length > 2 ){
				activity_search.search = text;
				searchTable( activity_search );
			}else{
				getTransactions( activity_search );
			}

		});

		$("#rangePicker_start").text( rangePicker_start );
		$("#rangePicker_end").text( rangePicker_end );

	}, 100);
}

function setYear( num ){
	console.log(num);
	$(".activity-date-header .year-selector a").removeClass('active');
	$(".activity-date-header .year-selector:nth-child(" + (num+1) + ") a").addClass('active');
	$( '.showCustomPickerTrue' ).fadeIn();
	$( '.showCustomPickerFalse' ).hide();
	
	initializeRangeSlider( );

	year_active = num;

	if( num == 1 ){
		yearToday = moment().format('YYYY');
	}else{
		yearToday = moment().subtract(1,'years').format('YYYY');
	}

	var range_data = date_slider.getValue();

	monthToday = range_data[0];
	monthToday2 = range_data[1];

	var text = $( ".search-table" ).val();
	var activity_search = getFirstEndDate( range_data[0], range_data[1] );

	if( text.length > 2 ){
		activity_search.search = text;
		searchTable( activity_search );
	}else{
		getTransactions( activity_search );
	}
}


function initializeRangeSlider( ){

	date_slider = new Slider("#timeframe-range", { 
		id: "timeframe-range", 
		min: 1, 
		max: 12, 
		range: true, 
		value: [1, parseInt(monthToday2)],
		// value: [parseInt(monthToday), parseInt(monthToday2)],
		ticks: [1,2,3,4,5,6,7,8,9,10,11,12],
		ticks_labels: ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'],
		tooltip : 'hide',
		ticks_tooltip : false,
	});

	var slide_trap = null;

	$( '#timeframe-range' ).on('slideStop', function(ev){
		clearTimeout(slide_trap);

		slide_trap = setTimeout(function() {
			var text = $( ".search-table" ).val();
			var range_data = date_slider.getValue();

			var activity_search = getFirstEndDate( range_data[0], range_data[1] );
			console.log(activity_search);

			if( text.length > 2 ){
				activity_search.search = text;
				searchTable( activity_search );
			}else{
				getTransactions( activity_search );
			}
		}, 800);
	});
}

setTimeout(function() {
	$( '.trans-view-page-container' ).show();

	initializeRangeSlider();

	$( '#hide-trans-history-invoice' ).on('click', function(ev){
		$( ".trans-tbl-box" ).fadeIn();
		$( ".trans-invoice-box" ).hide();

		var date_slider = new Slider("#timeframe-range", { 
			id: "timeframe-range", 
			min: 1, 
			max: 12, 
			range: true, 
			value: [parseInt(monthToday), parseInt(monthToday2)],
			ticks: [1,2,3,4,5,6,7,8,9,10,11,12],
			ticks_labels: ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'],
			tooltip : 'hide',
			ticks_tooltip : false,
		});

		setHeight();
	});

	$( '#trans-history-down' ).on('click', function(ev){
		$( ".trans-tbl-box" ).hide();
		$( ".trans-invoice-box" ).fadeIn();
		$( '#pdf-print-invoice tbody' ).html('');
		for( var i = 0; i < list.length; i++  ){

			var append_html = '<tr>' +
						'<td>' + list[i].date_of_transaction + '</td>' +
						'<td>' +
							list[i].transaction_id + 
							'<br />';
							
			append_html += ( list[i].transaction_status != 'null' && list[i].transaction_status != null ) ? '<label class="label label-success label-custom" >' + list[i].transaction_status + '</label>' : '';

			append_html += '</td>' +
						'<td>' + list[i].user_name + '</td>' +
						// '<td>' + list[i].NRIC + '</td>' +
						'<td>' + list[i].procedure_name + '</td>' +
						'<td><span style="text-transform: uppercase">' + list[i].currency_type + '</span> ' + list[i].mednefits_fee + '</td>' +
						'<td><span style="text-transform: uppercase">' + list[i].currency_type + '</span> ' + list[i].mednefits_credits + '</td>' +
						'<td><span style="text-transform: uppercase">' + list[i].currency_type + '</span> ' + list[i].cash + '</td>' +
					'</tr>';

    	$( '#pdf-print-invoice tbody' ).append( append_html );

    	setHeight();

    	if( (list.length-1) == i ){
    		setTimeout(function() {
    			var text = $( ".search-table" ).val();
    			console.log( rangePicker_start );
    			console.log( rangePicker_end );
    			if( text.length == 0 ){
    				// window.location.href = window.base_url+"clinic/download_transaction_lists?start="+moment(rangePicker_start, 'DD/MM/YYYY').format('YYYY-MM-DD')+"&end="+moment(rangePicker_end, 'DD/MM/YYYY').format('YYYY-MM-DD');
    				window.open(window.base_url+"clinic/download_transaction_lists?start="+moment(rangePicker_start, 'DD/MM/YYYY').format('YYYY-MM-DD')+"&end="+moment(rangePicker_end, 'DD/MM/YYYY').format('YYYY-MM-DD'), '_blank');
    				// console.log( window.base_url+"clinic/download_transaction_lists?start="+moment(rangePicker_start, 'DD/MM/YYYY').format('YYYY-MM-DD')+"&end="+moment(rangePicker_end, 'DD/MM/YYYY').format('YYYY-MM-DD') );
    			}else{
    				// window.location.href= window.base_url+"clinic/download_transaction_lists?start="+moment(rangePicker_start, 'DD/MM/YYYY').format('YYYY-MM-DD')+"&end="+moment(rangePicker_end, 'DD/MM/YYYY').format('YYYY-MM-DD')+"&nric=" + text;
    				// window.open(window.base_url+"clinic/download_transaction_lists?start="+moment(rangePicker_start, 'DD/MM/YYYY').format('YYYY-MM-DD')+"&end="+moment(rangePicker_end, 'DD/MM/YYYY').format('YYYY-MM-DD')+"&nric=" + text, '_blank');
    				window.open(window.base_url+"clinic/download_transaction_lists?start="+moment(rangePicker_start, 'DD/MM/YYYY').format('YYYY-MM-DD')+"&end="+moment(rangePicker_end, 'DD/MM/YYYY').format('YYYY-MM-DD')+ text, '_blank');
    				// console.log( window.base_url+"clinic/download_transaction_lists?start="+moment(rangePicker_start, 'DD/MM/YYYY').format('YYYY-MM-DD')+"&end="+moment(rangePicker_end, 'DD/MM/YYYY').format('YYYY-MM-DD')+"&nric=" + text);
    			}
    		}, 500);
    	}
    }

		
	});

	$( '#trans-csv-down' ).on('click', function(ev){
		downloadCSV({ filename: "transaction-history.csv" });
	});

	$( '.search-table' ).keyup(function(){
		var text = $( ".search-table" ).val();
		console.log( text );
		if( text.length == 0 ){
			var range_data = date_slider.getValue();
			var activity_search = getFirstEndDate( range_data[0], range_data[1] );
			console.log(activity_search);
			getTransactions( activity_search );
		}
	});

	$( '.btn-search-tbl' ).click(function(){
		var text = $( ".search-table" ).val();
		var range_data = date_slider.getValue();
		var activity_search = null;
		
		if( year_active == 3 ){
			activity_search = {
		  	start: moment(rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD'),
				end: moment(rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD'),
		  };
		}else{
			activity_search = getFirstEndDate( range_data[0], range_data[1] );
		}
		
		// console.log(activity_search);
		if( text.length > 0 ){
			activity_search.search = text;
			searchTable( activity_search );
		}else{
			activity_search.search = "";
			searchTable( activity_search );
		}
	});

	var range_data = date_slider.getValue();
	var activity_search = getFirstEndDate( range_data[0], range_data[1] );
	console.log(activity_search);
	getTransactions( activity_search );

	
	setHeight();
}, 500);

function onLoad(){
	$(".activity-date-header .year-selector:nth-child(" + (2) + ") a").addClass('active');
	// $( '#setting-nav-panel' ).css('width','90%');
	$( '.trans-view-page-container' ).hide();
}

onLoad();