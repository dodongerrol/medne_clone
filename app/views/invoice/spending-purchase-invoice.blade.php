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
        position: relative;
      }

      .invoice-content .header{
        border-bottom: 1px solid #DCDFE0;
        padding: 0px 30px 20px 30px;
        overflow: hidden;
        z-index: 10;
      }

      .invoice-content .header .item{
        display: inline-block;
        width: 49.5%;
        vertical-align: top;
      }

      .invoice-content .header .item p{
        margin: 0;
      }

      .bill-to{
        /*border-bottom: 1px solid #DCDFE0;*/
        padding: 12px 0px 0px 30px;
        overflow: hidden;
        width: 100%;
        display: inline-block;
        z-index: 10;
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
        padding: 10px 0;
        padding-right: 30px;
      }

      .bill-to .right-wrapper p {
        margin-left: 40px;
        margin-bottom: 10px;
      }

      .bill-to .right-wrapper label {
        width: 140px;
        text-align: right;
        margin-right: 15px;
        display: inline-block;
      }

      .invoice-content table{
        border-collapse: collapse;
        z-index: 10;
      }

      .invoice-content table .thead th {
        text-align: center;
        background: #0392CF;
        color: #FFF;
        border-color: #0392CF;
        padding: 8px 0;
      }

      .invoice-content table .tbody td {
        /* padding: 12px 8px; */
        /* padding: 0px 8px 0 8px; */
        padding-bottom: 30px;
      }

      .invoice-content table .tbody td p{
        margin: 0 0 3px 0;
      }

      .invoice-content table .tbody td p.left-space-20{
        padding-left: 20px;
      }

      .total{
        height: 300px;
      }

      .total p label {
        /* width: 180px; */
        text-align: right;
        display: inline-block;
        margin-right: 15px;
      }

      /*.notes p{
        margin:0;
        font-size: 12px;
      }*/

      .notes{
        position: relative;
      }

      .notes .item-col{
        width: 70%;
        display: inline-block;
        vertical-align: top;
        height: 220px;
      }
      .notes .item-col2{
        width: 27%;
        text-align: right;
        display: inline-block;
        vertical-align: top;
        padding-top: 50px;
        height: 130px;
      }

      .notes .stamp{
        width: 120px;
        height: 120px;
      }

      .copyright{
        padding-bottom: 20px;
      }
    </style>
  </head><body>
    <div class="invoice-content">
      @if($paid)
        <img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/images/paid-text.png" style="position: absolute;left: 25%;top: 2%;z-index: 1;width: 350px;">
      @endif
      <div class="header">
        <div class="item">
          <img src="https://mednefits.s3-ap-southeast-1.amazonaws.com/images/mobile-logo-blue-latest.png" style="width: 250px;margin-top: 65px;">
        </div>

        <div class="text-right item">
          <h1 style="font-size: 35px !important;color: #000 !important;font-family: 'Open Sans', sans-serif !important;margin-bottom: 0px;">INVOICE</h1>
          <p style="font-weight: 700;margin-top: 10px;">Medicloud Pte Ltd </p>

          @if($currency_type == "SGD")
            <p>7 Temasek Boulevard</p>
            <p>#18-02 Suntec Tower One</p>
            <p>038987</p>
            <p style="margin-top: 10px;">Singapore</p>
            <p>+65 3163 5403</p>
            <p>mednefits.com</p>
          @else
            <p>Mednefits Sdn Bhd</p>
            <p>Komune, Level 2,</p>
            <p>No. 20, Jalan Kerinchi Kiri 3,</p>
            <p>59200, Kuala Lumpur,</p>
            <p>Malaysia</p>
          @endif
        </div>
      </div>

      <div class="bill-to">
        <div class="item left-wrapper" >
          <p style="color: #aaa;margin: 0;"><b>BILL TO</b></p>
          <p>{{ $company_name }}</p>
        <p>{{ $contact_name }}</p>
        <p>{{ $company_address }}, {{ $postal }}</p>
        <p style="margin-top: 10px;">{{ $contact_number }}</p>
        <p>{{ $contact_email }}</p>
        </div>
        <div class="item right-wrapper" >
          <p><label>Invoice Number: </label> {{ $invoice_number }}</p>
          <p><label>Invoice Date: </label> {{ $invoice_date }}</p>
          <p><label>Payment Due: </label> {{ $invoice_due }}</p>
          @if(isset($payment_date))
          <p><label>Payment Date: </label> {{ $payment_date }}</p>
          @endif
          <p style="background: #eee;"><label>Amount Due: </label> <b>{{ $currency_type }} {{ $amount_due }}</b></p>
        </div>
      </div>

      <table class="table table-responsive text-center" style="width: 100%;border-collapse: collapse;border: none;" cellspacing="0" cellpadding="0">
        <tr class="thead">
          <th style="text-align: left !important;padding-left: 30px;width: 350px;">Description</th>
          <th style="text-align: right;">Quantity</th>
          <th style="text-align: right;">Unit Price</th>
          <th style="text-align: right;padding-right: 40px;">Amount({{ $currency_type }})</th>
        </tr>

        <tr class="tbody">
          <td style="text-align: left !important;padding-left: 30px;padding-top: 20px;">
            <p style="margin-bottom: 10px;"><b>{{ $plan_type }}</b></p>
            <p>Account Type: {{ $account_type }}</p>
            <p>Date of Credits Purchase: {{ $plan_start }}</p>
            <!-- <p>Billing Frequency: Annual</p>
            <p>Start Date: {{ $plan_start }}</p>
            <p>End Date: {{ $plan_end }}</p>
            <p>Plan Duration: {{ $duration }}</p>
            <p>Active Type: {{ $account_type }}</p> -->
          </td>
          <td></td>
          <td></td>
          <td></td>
        </tr>

      @if($medical_spending_account)
        <tr class="tbody">
          <td style="text-align: left !important;padding-left: 30px;">
            <p><b>Medical Spending Account (Pre-paid):</b></p>
            <p>Date of Credist Expiry: {{ $plan_end }}</p>
            <p>Purchased Credits: {{ $currency_type }} {{$medical_credits_purchase}}</p>
            <p>Bonus Credits: {{ $currency_type }} {{$medical_credit_bonus}}</p>
            <p style="border-bottom: 1px solid #bbb;padding-bottom: 5px;">Total Credits: {{ $currency_type }} {{$medical_total_credits}}</p>
            <p style="border-bottom: 1px solid #bbb;padding-bottom: 5px;">Discounts</p>
          </td>
          <td style="text-align: right;">
            <p style="height: 91px;"></p>
            <p style="border-bottom: 1px solid #bbb;padding-bottom: 4px;">1.00</p>
            <p style="border-bottom: 1px solid #bbb;padding-bottom: 5px;">1.00</p>
          </td>
          <td style="text-align: right;">
            <p style="height: 91px;"></p>
            <p style="border-bottom: 1px solid #bbb;padding-bottom: 4px;">{{ $medical_total_credits }}</p>
            <p style="border-bottom: 1px solid #bbb;padding-bottom: 5px;">({{ $medical_credit_bonus }})</p>
          </td>
          <td style="vertical-align: top;text-align: right;padding-right: 40px;">
            <p style="height: 91px;"></p>
            <p style="border-bottom: 1px solid #bbb;padding-bottom: 4px;">{{ $medical_total_credits }}</p>
            <p style="border-bottom: 1px solid #bbb;padding-bottom: 5px;">({{ $medical_credit_bonus }})</p>
          </td>
        </tr>
      @endif
      
      @if($wellness_spending_account)
        <tr class="tbody">
          <td style="text-align: left !important;padding-left: 30px;">
            <p><b>Wellness Spending Account (Pre-paid):</b></p>
            <p>Date of Credits Expiry: {{ $plan_end }}</p>
            <p>Purchased Credits: {{ $currency_type }} {{$wellness_credits_purchase}}</p>
            <p>Bonus Credits: {{ $currency_type }} {{$wellness_credit_bonus}}</p>
            <p style="border-bottom: 1px solid #bbb;padding-bottom: 5px;">Total Credits: {{ $currency_type }} {{$wellness_total_credits}}</p>
            <p style="border-bottom: 1px solid #bbb;padding-bottom: 5px;">Discounts</p>
          </td>
          <td style="text-align: right;">
            <p style="height: 91px;"></p>
            <p style="border-bottom: 1px solid #bbb;padding-bottom: 4px;">1.00</p>
            <p style="border-bottom: 1px solid #bbb;padding-bottom: 5px;">1.00</p>
          </td>
          <td style="text-align: right;">
            <p style="height: 91px;"></p>
            <p style="border-bottom: 1px solid #bbb;padding-bottom: 4px;">{{ $wellness_total_credits }}</p>
            <p style="border-bottom: 1px solid #bbb;padding-bottom: 5px;">({{ $wellness_credit_bonus }})</p>
          </td>
          <td style="vertical-align: top;text-align: right;padding-right: 40px;">
            <p style="height: 91px;"></p>
            <p style="border-bottom: 1px solid #bbb;padding-bottom: 4px;">{{ $wellness_total_credits }}</p>
            <p style="border-bottom: 1px solid #bbb;padding-bottom: 5px;">({{ $wellness_credit_bonus }})</p>
          </td>
        </tr>
      @endif

      </table>

      <div class="col-md-12 total" style="width: 100%;position: relative;height: 80px;padding: 0;">
        <div style="width: 64%;display: inline-block;"></div> 
        <div style="width: 35%;display: inline-block;">
          <div style="display: inline-block;width: 100%;padding-right: 20px;">
            <p style="margin-bottom: 5px;margin-top: 10px;text-align: right;padding-right: 15px;">
              <span style="margin-right: 10px;vertical-align: middle;">Subtotal:</span> 
              <span style="vertical-align: middle;width: 120px;">{{ $currency_type }} {{ $total }}</span>
            </p>

            <div style="border-bottom: 1px solid #bbb;display: inline-block;width: 100%;padding-bottom: 5px;height: 1px"></div>

            <p style="margin-top: 5px;text-align: right;padding-right: 15px;">
              <span style="margin-right: 10px;vertical-align: middle;">Amount Due:</span> 
              <span style="vertical-align: middle;width: 120px;"><b>{{ $currency_type }} {{ $amount_due }}</b></span>
            </p>
          </div>
        </div>
      </div>

      <div class="col-md-12 notes">
        <div class="item-col">
        @if($currency_type == "SGD")
          <!-- <p style="margin-bottom: 10px;font-size: 13px;"><b>Notes</b></p> -->
          <!-- <p style="font-size: 13px;"><b>Please make cheques payable to:</b></p>
          <p>Medicloud Pte Ltd</p>
          <p>7 Temasek Boulevard</p>
          <p>#18-02 Suntec Tower One, S038987</p> -->
          <p class="payment-info-title font-medium2 font-20 color-blue-custom2 no-margin">Payment Information</p>

          <div class="white-space-10"></div>

          <!-- <p style="font-size: 13px;"><b>Corporate PayNow</b></p>
          <p>UEN: 201415681W</p>

          <p style="font-size: 13px;margin-top: 10px;"><b>Or Bank Transfer to:</b></p>
          <p>Bank Name: UNITED OVERSEAS BANK LIMITED</p>
          <p>Bank A/C: 374-3069-399</p>
          <p>Swift Code: UOVBSGSG - UNITED OVERSEAS BANK LIMITED</p>
          <p>Bank Address: 3 Temasek Boulevard #02-735/736 Suntec City mall Sinagpore 038983</p> -->
          <!-- <p>Account Name: Medicloud Pte Ltd</p>
          <p>Account No.: 3743069399</p>
          <p>ACRA 201415681W</p> -->
          <p class="bank-info-title font-medium2 color-black2 no-margin">Corporate PayNow</p>
          <p class="bank-info-text no-margin weight-700 line-height-1">UEN: 201415681W</p>

          <div class="white-space-10"></div>
          <p class="bank-info-title font-medium2 color-black2 no-margin">Bank Transfer:</p>

          <p class="bank-info-text no-margin weight-700 line-height-1">Account Name: Medicloud Pte Ltd</p>
          <p class="bank-info-text font-medium2 no-margin">Bank Name: UNITED OVERSEAS BANK LIMITED</p>
          <p class="bank-info-text font-medium2 no-margin">Bank A/C: 374-3069-399</p>
          <p class="bank-info-text font-medium2 no-margin">Swift Code: UOVBSGSG - UNITED OVERSEAS BANK LIMITED</p>
          <p class="bank-info-text font-medium2 color-black2 no-margin">Bank Address: 3 Temasek Boulevard #02-735/736 Suntec City Mall Singapore 038987</p>

          @if(isset($remarks))
            @if($remarks && $remarks != 'NULL')
            <p style="margin: 10px 0 0 0;font-size: 11px;">Note: {{ $remarks }}</p>
            @endif
          @endif

          <p style="margin: 15px 0 0 0;font-size: 15px;">Please contact us for any questions related to your invoice/contract at support@mednefits.com</p>
          <p style="font-size: 12px;">Please send all payment advice to finance@mednefits.com</p>
        @else

          <p style="font-weight: 700;font-size: 18px;line-height: 16px;margin: 0 0 10px 0;">Due Date: {{$invoice_due}}</p>
          <p style="font-size: 14px;line-height: 14px;margin: 0 0 35px 0;">Payment Information:</p>

          <p style="font-size: 14px;line-height: 14px;margin: 0 0 10px 0;">Bank Transfer:</p>
          <p style="font-size: 14px;line-height: 14px;margin: 0 0 10px 0;">Bank: UOB</p>
          
          
          <p style="font-size: 14px;line-height: 14px;margin: 0 0 10px 0;">Account Name: Mednefits Sdn. Bhd.</p>
          <p style="font-size: 14px;line-height: 14px;margin: 0 0 25px 0;">Account Number: 2213020031</p>
          
          <p style="font-weight: 700;font-size: 14px;line-height: 14px;margin: 0 0 10px 0;">Note: Please quote invoice number when submitting payment</p>
        @endif
        </div>

        @if($currency_type == "SGD")
        <div class="item-col2">
          <img class="stamp" src="https://s3-ap-southeast-1.amazonaws.com/mednefits/images/Mednefits_Company_Stamp-01.png">
        </div>
        @endif
      </div>

      <div class="col-md-12 copyright text-center">
        <h5 style="color: #999;"><b>&copy; 2020 Mednefits. All rights reserved</b></h5>
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

  .bank-info-title {
    font-family: 'Helvetica Medium';
  }

  .bank-info-text {
    color: #999;
    font-size: 14px;
  }

  .payment-info-title {
    color: #009EC8;
  }

  .notes-container .payment-info-title {
    font-size: 20px;
  }
</style>
