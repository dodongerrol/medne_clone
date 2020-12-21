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
        
      body{
        margin: 0;
        font-family: 'Helvetica Light',sans-serif;
        font-size: 16px;
        line-height: 1.42857143;
      }

      #main-template-wrapper{
        margin: 10px auto 20px auto;
        width: 881px;
        background: #fff;
        min-height: 1000px;
      }

      .top-content{
        /* width: 100%; */
        position: relative;
        /* overflow: hidden; */
        border-left: 1px solid #ccc;
        border-right: 1px solid #ccc;
        border-top: 1px solid #ccc;
        /* min-height: 382px; */
        box-sizing: border-box;
      }

      .top-content .logo-container{
        text-align: center;
        position: relative;
        height: 77px;
        max-height: 77px;
        z-index: 9;
        padding-top: 12px;
        padding-left:70px;
        padding-right:70px;
      }
      
      .top-content .logo-container img{
        height: 130px;
        max-height: 130px;
        width: 130px;
        padding: 12px;
        border-radius: 50%;
        background-color: #FFF;
      }

      .top-content .header-content{
        text-align: center;
        color: #FFF !important;
        /* width: 100%; */
        overflow: hidden;
        position: relative;
        z-index: 5;
        background-color: #3E91C8;
        padding-top: 90px;
        padding-left: 60px;
        padding-right: 60px;
        /* min-height: 305px; */
        box-sizing: border-box;
      }

      .top-content .header-content p{
        margin: 0;
        color: #FFF !important;
      }

      .body-content{
        /*border-left: 1px solid #ccc;*/
        /*border-right: 1px solid #ccc;*/
        /*border-bottom: 1px solid #ccc;*/
        min-height: 725px;
        background: #FFF;
        box-sizing: border-box;
      }

      .body-content .trans-content{
        background: #F7F7F7;
        padding: 0px 70px;
        /* width:  100%; */
        border-left: 1px solid #ccc;
        border-right: 1px solid #ccc;
        /* height: 140px; */
        box-sizing: border-box;
      }

      .trans-content .item{
        display: inline-block;
        width: 100%;
        /* height: 70px; */
        /* margin: 10px 0 !important; */
        padding: 20px 0;
      }

      .trans-content .item .one{
        display:inline-block;
        width: 30px;
        margin-right: 45px;
        vertical-align: top;
      }

      .trans-content .item .one img{
        width: 30px;
      }

      .trans-content .item .two{
        display: inline-block;
        color: #777;
        width: 370px;
        font-size: 22px;
        /* margin-right: 45px; */
      }

      .trans-content .item .three{
        display: inline-block;
        color: #333;
        font-weight: 700;
        font-size: 22px;
      }

      .body-content .receipt-details{
        padding: 30px 70px 10px 70px;
        min-height: 100px;
        /* width: 100%; */
        border-left: 1px solid #ccc;
        border-right: 1px solid #ccc;
        box-sizing: border-box;
      }

      .body-content .receipt-details .row{
        width: 100%;
        display: block;
      }

      .body-content .receipt-details .row .item{
        width: 285px;
        display: inline-block;
        vertical-align: top;
      }

      .body-content .receipt-details .row .one{
        /* width: 445px; */
        width: 380px;
        margin-right: 65px;
      }

      .body-content .item_service{
        padding: 30px 70px;
        min-height: 100px;
        overflow: hidden;
        /* width: 100%; */
        border-left: 1px solid #ccc;
        border-right: 1px solid #ccc;
        border-bottom: 1px solid #ccc;
        box-sizing: border-box;
      }

      .billing-details-header {
        align-items: center;
        padding: 0 70px;
        border-left: 1px solid #ccc;
        border-right: 1px solid #ccc;
        min-height: 30px;
        box-sizing: border-box;
      }

      .billing-details-header span {
        color: #595959;
        width: 139px;
        display: inline-block;
        font-size: 22px;
        margin-right: 34px;
        vertical-align: middle;
      }

      .billing-details-header .custom-border{
        border-top: 1px solid #ddd;
        width: 555px;
        display: inline-block;
        vertical-align: middle;
      }

      .billing-details-body-container .row-grid {
        width: 100%;
        padding: 0 0 30px;
      }

      .billing-details-body-container .row-grid.last{
        border-bottom: 1px solid #ddd;
      }

      .billing-details-body-container .row-grid .title {
        font-size: 22px;
        color: #999;
        width: 445px;
        display: inline-block;
      }

      .billing-details-body-container .row-grid .amount {
        color: #333;
        font-size: 22px;
        font-weight: 700;
        display: inline-block;
        width: 285px;
      }

      .billing-details-paid {
        margin: 20px 0 0;
      }



      .contact-content{
        /* height: 150px; */
        width: 100%;
        padding: 30px 0;
      }

      .contact-content .item{
        display: inline-block;
        vertical-align: top;
        width: 285px;
      }
      
      .contact-content .one{
        width: 445px;
      }
      
      .contact-content .item .social-img{
        display: inline-block;
        vertical-align: middle;
        margin-left: 10px;
      }
      
      .contact-content .item .social-img a img{
        width: 50px;
      }

      .contact-img {
        width: 45px;
      }

      .contact-support-item {
        /* display: flex; */
        align-items: center;
        color: #848484;
        font-size: 22px;
      }

      .contact-support-item >span{
        display: inline-block;
        vertical-align: middle;
      }
      
      .contact-support-item .child-one {
        margin: 0 20px 0 0;
        height: 45px;
        max-height: 45px;
      }
    </style>
  </head><body>
    <div id="main-template-wrapper" >
      <div class="top-content">
        <div class="logo-container">
          <img src="https://mednefits.s3-ap-southeast-1.amazonaws.com/e-template-img/new_logo_icon.png">
        </div>
        <div class="header-content">
          <p style="font-size: 24px;line-height: 29px;margin-bottom: 20px;">
            Here's the payment receipt for your visit at <span style="font-weight: 700">{{ $health_provider_name }}</span>.
          </p>
          <p style="font-size: 19px; width: 537px; margin: 0 auto 23px auto;">
            You can also view your receipts under the History section in Mednefits app.
          </p>
          <p style="font-size: 45px;font-weight: 700;margin-bottom: 20px;">
            Total: <span>{{ $currency_symbol }}</span> <span>{{ $credits }}</span>
          </p>

        </div>
      </div>

      <div class="body-content">
        <div class="trans-content">
          <div class="item" style="border-bottom: 1px solid #ddd;">
            <div class="one">
              <img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/e-template-img/Trans-ID---Mednefits-Credits-Email.png"/>
            </div>
            <div class="two">
              Transaction ID
            </div>
            <div class="three">
              {{ $transaction_id }}
            </div>
          </div>
          <br>
          <div class="item" >
            <div class="one">
              <img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/e-template-img/clock.png" />
            </div>
            <div class="two">
              Transaction Date
            </div>
            <div class="three">
              {{ $transaction_date }}
            </div>
          </div>
        </div>

        <div class="receipt-details">
          <div class="row">
            <div class="item one">
              <div style="font-size: 22px;color: #999;margin-bottom: 13px;">
                Health Provider
              </div>
              <p style="color: #333;font-size: 22px;font-weight: 700;margin-top: 0;">
                <span class="health-provider-name" style="margin: 0 0 13px">{{ $health_provider_name }}</span> 
                <br>
                {{ $health_provider_address }} {{ $health_provider_city }},
                {{ $health_provider_country }} {{ $health_provider_postal }}
                <br>
                {{ $health_provider_phone }}
              </p>
            </div>

            <div class="item">
              <div style="font-size: 22px;color: #999;margin-bottom: 13px;">
                Service
              </div>
              <p style="color: #333;font-size: 22px;font-weight: 700;margin-top: 0;">
                {{ $service }} <br>
              </p>
            </div>
          </div>

          <div class="row">
            <div class="item one">
              <div style="font-size: 22px;color: #999;margin-bottom: 13px;">
                Member
              </div>
              <p style="color: #333;font-size: 22px;font-weight: 700;margin-top: 0;">
                {{ $member }}
              </p>
            </div>

            <div class="item">
              <div style="font-size: 22px;color: #999;margin-bottom: 13px;">
                Cap Per Visit
              </div>
              <p style="color: #333;font-size: 22px;font-weight: 700;margin-top: 0;">
                @if($cap_per_visit_status)
                  {{ $currency_symbol }}
                @endif

                {{ $cap_per_visit }}
              </p>
            </div>
          </div>
        </div>

        <div class="billing-details-header">
          <span>Billing details</span>
          <div class="custom-border"></div>
        </div>

        <div class="item_service">
          <div class="billing-details-body-container">
            <div class="row-grid">
              <div class="item title">
                Bill Amount
              </div>
              <div class="item amount">
                <span>{{ $currency_symbol }}</span> <span>{{ $bill_amount }}</span>
              </div>
            </div>
            <div class="row-grid">
              <div class="item title">
                Consultation Fee
              </div>
              <div class="item amount">
                <span>{{ $currency_symbol }}</span> <span>{{ $consultation }}</span>
              </div>
            </div>
            <div class="row-grid last">
              <div class="item title">
                Total Amount
              </div>
              <div class="item amount">
                <span>{{ $currency_symbol }}</span> <span>{{ $total_amount }}</span>
              </div>
            </div>
          </div>

          <div class="billing-details-body-container billing-details-paid">
            <div class="row-grid">
              <div class="item title">
                Paid by Credits
              </div>
              <div class="item amount">
                <span>{{ $currency_symbol }}</span> <span>{{$paid_by_credits}}</span>
              </div>
            </div>
            <div class="row-grid last">
              <div class="item title">
                Paid by Cash
              </div>
              <div class="item amount">
                <span>{{ $currency_symbol }}</span> <span>{{$paid_by_cash}}</span>
              </div>
            </div>
          </div>

          <div class="contact-content">
            <div class="item one">
              <div style="font-size: 22px;color: #777;margin-bottom: 20px;">
                Contact support
              </div>
              <div class="contact-support-item">
                <span class="child-one">
                  <img class="contact-img" src="https://mednefits.s3-ap-southeast-1.amazonaws.com/e-template-img/telephone.png">
                </span>
                @if($currency_symbol == "SGD")
                  <span style="text-decoration: underline;">+65 3163 5403 </span> <span> <span style="margin: 0 0 0 5px;"></span></span>
                @else 
                  <span style="text-decoration: underline;">+65 3163 5403 </span> <span> <span style="margin: 0 0 0 5px;">or</span> <span style="text-decoration: underline;">+60 330 995 774</span></span>
                @endif
              </div>
              <br>
              <div class="contact-support-item">
                <span class="child-one">
                  <img class="contact-img" src="https://mednefits.s3-ap-southeast-1.amazonaws.com/e-template-img/envelope.png">
                </span>
                <span style="text-decoration: none;">support@mednefits.com</span>
              </div>
            </div>

            <div class="item" style="text-align: right;">
              <div style="font-size: 22px;color: #777;margin-bottom: 20px;">
                Connect with us at
              </div>
              <div class="social-img">
                <a href="https://www.linkedin.com/company/mednefits/" style="text-decoration: none;">
                  <img src="https://mednefits.s3-ap-southeast-1.amazonaws.com/e-template-img/linkedin.png"/>
                </a>
              </div>
              <div class="social-img">
                <a href="https://www.instagram.com/mednefits/" style="text-decoration: none;">
                  <img src="https://mednefits.s3-ap-southeast-1.amazonaws.com/e-template-img/instagram.png"/>
                </a>
              </div>
              <div class="social-img">
                <a href="https://www.facebook.com/Mednefits/" style="text-decoration: none;">
                  <img src="https://mednefits.s3-ap-southeast-1.amazonaws.com/e-template-img/facebook.png"/>
                </a>
              </div>
            </div>
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
  .text-right {
    text-align: right;
  }
  .text-center {
    text-align: center;
  }
</style>