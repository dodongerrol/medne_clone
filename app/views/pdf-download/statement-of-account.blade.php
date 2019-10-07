<!DOCTYPE html>
<html><head>
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Statement</title>
    <style type="text/css">
      @page { margin: 10px; }

      * {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
      }
        
      .col-md-12 {
        width: 100%;
        position: relative;
        min-height: 1px;
        padding-left: 15px;
        padding-right: 15px;
      }
      body{
        /*background-color: #f0f0f0;*/
        margin: 0;
        font-family: 'Helvetica Light',sans-serif;
        font-size: 14px;
        line-height: 1.42857143;
      }

      .invoice-content{
        border: 1px solid #EEE;
        overflow: hidden;
        margin: 10px auto;
        width: 98%;
        background: #F8FAFC;
      }

      .invoice-content .header{
        border-bottom: 1px solid #F1F1F1;
        padding: 20px 30px 20px 30px;
        overflow: hidden;
      }

      .invoice-content .header .item{
        display: inline-block;
        width: 49.5%;
        vertical-align: top;
      }

      .bill-to{
        /*border-bottom: 1px solid #DCDFE0;*/
        padding: 25px 0px 25px 30px;
        overflow: hidden;
        width: 100%;
        display: inline-block;
      }

      .bill-to .item{
        display: inline-block;
        width: 49.5%;
        vertical-align: top;
      }

      .bill-to .item p{
        margin: 0;
        color: #333;
      }

      .bill-to .right-wrapper {
        /*background: #eee;*/
        /*padding: 10px 0;*/
        padding-right: 30px;
      }

      .bill-to .right-wrapper label {
        width: 210px;
        text-align: right;
        margin-right: 15px;
        display: inline-block;
      }

      .invoice-content table{
        border-collapse: collapse;
      }

      .invoice-content table .thead th {
        text-align: left;
        padding: 8px;
      }

      .invoice-content table .tbody td {
        padding: 15px 8px;
        text-align: left;
      }

      .invoice-content table .tbody td p{
        margin: 0;
      }

      .total{
        height: 300px;
      }

      .total p label {
        width: 145px;
        display: inline-block;
        margin-right: 15px;
      }

      .notes{
        position: relative;
      }

      .notes p{
        font-size: 12px;
        margin: 0 30px;
      }

      .notes .item-col{
        width: 70%;
        display: inline-block;
        vertical-align: top;
        height: 280px;
      }
      .notes .item-col2{
        width: 27%;
        text-align: right;
        display: inline-block;
        vertical-align: top;
        padding-top: 70px;
        height: 180px;
      }

      .notes .stamp{
        width: 150px;
        height: 150px;
      }

      .copyright{
        padding-bottom: 20px;
      }
    </style>
  </head><body>
    <div class="invoice-content">
      <div class="header">

        <div class="col-md-12 text-center">
          <h1 style="font-size: 35px !important;color: #999 !important;font-family: 'Open Sans', sans-serif !important;margin-bottom: 0px;">STATEMENT OF ACCOUNT</h1>
          <p style="margin-top: 5px;">(Generated on {{ date('M d, Y', strtotime($statement->created_at)) }} )</p>
        </div>

        <div class="col-md-12">
          <div class="item">
            <div id="clinic-logo-container" style="text-align: left;">
              <img src="{{ $clinic->image }}" style="max-width: 250px;max-height: 135px;">
            </div>
          </div>
          <div class="item" style="text-align: right;">
            <p class="statement_bank_name" style="margin-bottom: 10px;font-weight: 700;">{{ $bank_details['bank_name'] }}</p>
            <p class="statement_bank_address" style="font-weight: 700;">{{ $bank_details['billing_address'] }}</p>
          </div>
        </div>
      </div>

      <div class="bill-to">
        <div class="item left-wrapper" >
          <p style="margin: 0;">BILL TO</p>
          <p style="margin: 0 0 10px 0;">Medicloud Private Limited</p>
          <p>7 Temasek Boulevard</p>
          <p>#18-02 Suntec Tower One</p>
          <p>038987</p>
        </div>
        <div class="item right-wrapper" >
          <p style="margin: 0;text-align: right;">Account Summary</p>
          <p><label>Invoiced: </label> <span style="float: right;display: inline-block;width: 115px;text-align: left;">${{ $total }}</span></p>
          <p><label>Payments: </label> <span style="float: right;display: inline-block;width: 115px;text-align: left;">(${{ $payment_record->amount_paid }})</span></p>
          <p><label>Ending Balance {{ date('M d, Y', strtotime($statement->created_at)) }}: </label> <span style="float: right;display: inline-block;width: 115px;text-align: left;">${{$ending_balance}}</span></p>
        </div>
      </div>

      <div style="width: 95%;margin:0 auto 20px auto;">
        <div style="background: #EEE;padding: 15px;color: #AFAFAF !important;text-align: center;">
          SHOWING ALL INVOICES AND PAYMENTS BETWEEN {{ $period }}
        </div>
      </div>
      
      <table class="table table-responsive text-center" style="border-bottom: 2px solid #DCDFE0;width: 95%;margin:0 auto;">
        <tr class="thead">
          <th>Date</th>
          <th>Details</th>
          <th style="text-align: right !important;">Amount</th>
          <th style="text-align: right !important;">Balance</th>
        </tr>

        <tr class="tbody">
          <td style="text-align: left !important;"><b>{{ date('M d, Y', strtotime($invoice_record['start_date'])) }}</b></td>
          <td><b>Invoice #{{ $payment_record->invoice_number }} (due {{ date('M d, Y', strtotime($invoice_due)) }})</b></td>
          <td style="text-align: right !important;"><b>S${{ $total }}  </b></td>
          <td style="text-align: right !important;"><b>S${{ $total }}</b></td>
        </tr>

        <tr class="tbody">
          <td style="text-align: left !important;"><b>{{ date('M d, Y', strtotime($invoice_record['end_date'])) }}</b></td>
          <td><b>Payment Invoice #{{ $payment_record->invoice_number }}</b></td>
          <td style="text-align: right !important;"><b>(S${{ $payment_record['amount_paid'] }})</b></td>
          <td style="text-align: right !important;"><b>S${{ $ending_balance }}</b></td>
        </tr>

        <tr class="tbody">
          <td style="text-align: left !important;"><b>{{ date('M d, Y', strtotime($statement->created_at)) }}</b></td>
          <td><b>Invoice #{{ $payment_record['invoice_number'] }} (due {{ date('M d, Y', strtotime($invoice_due)) }})</b></td>
          <td style="text-align: right !important;"><b></b></td>
          <!-- <td style="text-align: right !important;"><b>S${{ $ending_balance }}</b></td> -->
          <td></td>
        </tr>

      </table>

      <div class="col-md-12 total text-right" style="width: 94.5%;text-align: right;position: relative;height: 135px;">
        <div style="width: 250px;display: inline-block;position: absolute;right: 15px;top: 25px;">
          <p style="margin-bottom: 10px;">Amount due (SGD)</p>
          <p style="margin-top: 10px;">S${{ $ending_balance }}</p>
        </div>
      </div>

      <div class="col-md-12 notes">
        <div class="">
          <p style="margin-bottom: 10px;font-size: 14px;"><b>Notes</b></p>
          @if($payment_record['payment_date'])
          <p style="font-size: 14px;"><b>Payment Date :</b> {{ date('M d, Y', strtotime($payment_record['payment_date'])) }}</p>
          @endif
          @if($payment_record['transfer_referrence_number'])
          <p style="font-size: 14px;"><b>Transfer Reference Number :</b> {{ $payment_record['transfer_referrence_number'] }}</p>
          @endif
        </div>
      </div>

      <div class="col-md-12 copyright text-center">
        <h5 style="color: #999;"><b>&copy; 2019 Mednefits. All rights reserved</b></h5>
      </div>

    </div>
    </body></html>

<style type="text/css">
  p {
    display: block;
    -webkit-margin-before: 1em;
    -webkit-margin-after: 1em;
    -webkit-margin-start: 0px;
    -webkit-margin-end: 0px;
  }
  .pull-right{
    position: absolute;
    right: 0;
    top: 0;
  }
    
  .text-right {
    text-align: right;
  }
  .text-center {
    text-align: center;
  }
  .no-padding{
    padding: 0;
  }
  .color-gray {
    color: #777;
  }
  .color-black3 {
    color: #555 !important;
  }
  .color-blue-custom2 {
    color: #009EC8 !important;
  }
  .font-medium2 {
    font-family: 'HelveticaNeueMed', sans-serif !important;
  }
  .line-height-1 {
    line-height: 1.3;
  }
  .no-margin-top {
    margin-top: 0 !important;
  }
  .no-margin{
    margin: 0 !important;
  }
  .weight-700{
    font-weight: 700;
  }
  .font-20 {
    font-size: 20px !important;
  }
  .font-14{
    font-size: 14px;
  }
  .font-16{
    font-size: 16px;
  }
  .white-space-10{
    height: 10px;
    width: 100%;
  }
  .white-space-20{
    height: 20px;
    width: 100%;
  }

  .color-white{
    color: #FFF;
  }
</style>