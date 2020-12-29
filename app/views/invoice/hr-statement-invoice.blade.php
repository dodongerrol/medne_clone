<!DOCTYPE html>
<html><head>
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="shortcut icon" href="/assets/new_landing/images/favicon.ico" type="image/ico">
    <title>Company Spending Invoice</title>
    <style type="text/css">
      @page { margin: 50px 10px 0 10px; }

      * {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
      }

      .col-md-12 {
        width: 100%;
        float: left;
        position: relative;
        min-height: 1px;
        padding-left: 15px;
        padding-right: 15px;
      }

      body{
        /*background-color: #f0f0f0;*/
        font-family: 'Helvetica Light',sans-serif;
        font-size: 16px;
        line-height: 1.42857143;
      }
      .transac-invoice{
        margin: 0px auto;
        position: relative;
      }
      .invoice-wrapper{
        overflow: hidden;
        /*box-shadow: 1px 1px 10px #888888;*/
        background-color: #FFF;
        padding: 0px 20px 10px 20px;
        /*margin-bottom: 10px;*/
      }
      .transac-invoice .invoice-wrapper .header{
        border-bottom: 1px solid #DCDFE0;
        padding: 0px 10px 10px 10px;
        overflow: hidden;
        position: relative;
        height: 200px;
      }

      .invoice-wrapper .header .left-box{
        display: inline-block;
        width: 40%;
        height: 250px;
      }
      .invoice-wrapper .header .right-box{
        display: inline-block;
        /*width: 55%;*/
        width: 400px;
        position: absolute;
        right: 20px;
      }


      .transac-invoice .invoice-wrapper .bill-to{
        padding: 10px 10px 0px 10px;
        overflow: hidden;
        position: relative;
      }
      .left-details{
        display: inline-block;
        width: 40%;
      }
      .right-details{
        display: inline-block;
        /*width: 55%;*/
        width: 400px;
        position: absolute;
        right: 20px;
      }
      .right-details p{
        position: relative;
      }
      .right-details p > label {
        text-align: right;
        width: 200px;
        display: inline-block;
      }
      .right-details p > span {
        text-align: left;
        width: 200px;
        display: inline-block;
        margin-left: 20px;
        margin-right: 50px;
        color: #555;
        font-weight: 700;
        font-size: 14px;
        position: absolute;
      }
      .summary .charges-row:nth-child(odd) {
        background: #f1f1f1;
      }
      .summary .charges-row {
        overflow: hidden;
        padding: 20px 30px;
        position: relative;
      }

      .summary .charges-row p{
        position: relative;
      }
      .summary .total-due {
        margin-top: 0px;
        width: 350px;
        background: #0086D3;
        padding: 15px 30px;
        color: #eee;
        display: inline-block;
        text-align: left;
        margin-bottom: 100px;
        position: relative;
      }

      .summary .total-due p{
        position: relative;
      }

      .notes{
        position: relative;
      }

      .notes .stamp{
        width: 150px;
        height: 150px;
        position: absolute;
        right: 50px;
        bottom: 0;
      }
    </style>
  </head><body>
    <div class="transac-invoice">

      <div class="col-md-12 invoice-wrapper" id="pdf-print" style="">
        <div class="header">
          <div class="no-padding left-box">
            <img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/images/Mednefits_Logo_(BLUE).png" style="width: 250px;margin-top: 70px;">
          </div>
          <div class="text-right right-box no-padding">
            <h2 class="font-medium2 color-black3 no-margin-top line-height-1" style="font-size: 30px;margin-bottom: 5px;">INVOICE</h2>
            <p class="font-medium2 color-black2 weight-700 font-16 no-margin line-height-1">Medicloud Pte Ltd</p>
            <p class="color-gray weight-700 no-margin font-14 line-height-1">7 Temasek Boulevard</p>
            <p class="color-gray weight-700 no-margin font-14 line-height-1">#18-02 Suntec Tower</p>
            <p class="color-gray weight-700 no-margin font-14 line-height-1">038987</p>
            <p class="color-gray weight-700 no-margin font-14 line-height-1">Singapore</p>
            <div class="white-space-20"></div>
            <p class="color-gray weight-700 no-margin font-14 line-height-1">+65 3163 5403</p>
            <p class="color-gray weight-700 no-margin font-14 line-height-1">mednefits.com</p>
          </div>
        </div>
        <div class="bill-to">
          <div class="left-details no-padding">
            <p class="color-black3 font-15 no-margin font-medium2 weight-700 line-height-1">Team Benefits Statement</p>
            <p class="color-black3 font-15 no-margin font-medium2 weight-700 line-height-1">{{ $company }}</p>
            <p class="color-black3 font-15 no-margin font-medium2 weight-700 line-height-1">{{ $company_address }}</p>
            <div class="white-space-10"></div>
            <p class="color-black3 font-14 no-margin weight-700 line-height-1">{{ $statement_contact_name }}</p>
            <p class="color-black3 font-14 no-margin weight-700 line-height-1">{{ $statement_contact_number }}</p>
            <p class="color-black3 font-14 no-margin weight-700 line-height-1">{{ $statement_contact_email }}</p>
          </div>
          <div class="right-details no-padding">
            <p class="color-black no-margin">
              <label class="color-black3 font-15 font-medium2 weight-700 line-height-1">Statement Number:</label>
              <span class="invoice_number">{{ $statement_number }}</span>
            </p>
            <p class="color-black no-margin">
              <label class="color-black3 font-15 font-medium2 weight-700 line-height-1">Statement Date:</label>
              <span class="invoice_first_day">{{ $statement_date }}</span>
            </p>
            <p class="color-black no-margin">
              <label class="color-black3 font-15 font-medium2 weight-700 line-height-1">Payment Due:</label>
              <span class="invoice_last_day">{{ $statement_due }}</span>
            </p>
            @if($statement_status == 1)
            <p class="color-black no-margin">
              <label class="color-black3 font-15 font-medium2 weight-700 line-height-1">Payment Date:</label>
              <span class="invoice_last_day">{{ $paid_date }}</span>
            </p>
            @endif
            <p class="color-black no-margin">
              <label class="color-black3 font-15 font-medium2 weight-700 line-height-1">Amount Due (SGD):</label>
              <span>{{ strtoupper($currency_type) }} <span>{{ $total_due }}</span></span>
            </p>
          </div>
        </div>
        <div class="summary">
          <div class="col-md-12" >
            <h4 class="color-blue-custom2 weight-700 font-20" style="padding-bottom:10px;border-bottom: 2px solid #009EC8;color: #009EC8 !important;margin: 10px 0 !important;">Summary of Charges</h4>
            <div class="white-space-20"></div>
          </div>
          <div class="col-md-12">
            <div class="charges-row">
              <!-- <p class="color-black3 weight-700 no-margin">In-Network Spending Account Usage <span class="pull-right">{{ strtoupper($currency_type) }} {{ $statement_in_network_amount }}</span></p> -->
              <p class="color-black3 weight-700 no-margin">Panel Spending Account Usage <span class="pull-right">{{ strtoupper($currency_type) }} {{ $statement_in_network_amount }}</span></p>
              <p class="weight-700 no-margin" style="color:#777;padding-bottom: 15px;font-size: 14px;">Statement for {{ $statement_start_date }} - {{ $statement_end_date }}</p>
              @if($lite_plan && $consultation > 0)
              <p class="color-black3 weight-700 no-margin" style="padding: 0 0 0 50px;">Consultation Spent - General Practitioner <span class="pull-right">{{ strtoupper($currency_type) }} {{ $total_consultation }}</span></p>
              @endif
            </div>
            <div class="charges-row">
              <p class="color-blue-custom2 weight-700 no-margin">Sub Total <span class="pull-right">{{ strtoupper($currency_type) }} {{ $sub_total }}</span></p>
            </div>
          </div>
          <div class="col-md-12 text-right" style="position: relative;text-align: right;height: 70px;">
            <div style="position: absolute;right: 15px;top: 0;">
              <div class="total-due">
                <p class="font-medium2 weight-700 no-margin">Total Due <span class="pull-right">{{ strtoupper($currency_type) }} {{ $total_due }}</span></p>
              </div>
            </div>
          </div>
          <div class="col-md-12 notes" >
            <p class="font-medium2 font-20 color-blue-custom2 no-margin">Payment Information</p>
            <div class="white-space-10"></div>
            <p class="font-medium2 color-black2 no-margin">Corporate PayNow</p>
            <p class="color-gray font-14 no-margin weight-700 line-height-1">UEN: 201415681W</p>
            <div class="white-space-10"></div>
            <p class="font-medium2 color-black2 no-margin">Bank Transfer:</p>
            <p class="color-gray font-14 no-margin weight-700 line-height-1">Bank: UOB</p>
            <p class="color-gray font-14 no-margin weight-700 line-height-1">Account Name: Medicloud Pte Ltd</p>
            <p class="color-gray font-14 no-margin weight-700 line-height-1">Account Number: 3743069399</p>
            <p>Please send all payment advice to finance@mednefits.com</p>
            @if($payment_remarks)
            <p class="color-gray font-14 no-margin weight-700 line-height-1">Note: {{ $payment_remarks }}</p>
            @endif

            <img class="stamp pull-right" style="margin-top: 10%" src="https://s3-ap-southeast-1.amazonaws.com/mednefits/images/Mednefits_Company_Stamp-01.png">
          </div>
          <div class="text-center col-md-12" style="margin-top: 50px;">
            <h5 style="color: #999;"><b>&copy; 2020 Mednefits. All rights reserved</b></h5>
          </div>
        </div>
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
    /*float: right;*/
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
</style>
