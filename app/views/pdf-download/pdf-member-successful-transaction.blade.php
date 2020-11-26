<!DOCTYPE html>
<html><head>
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Statement</title>
    <style type="text/css">
      @page { 
        margin: 10px 10px 0 10px; 
      }

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
        /* margin: 10px auto 20px auto; */
        border: 1px solid #ccc;
        width: 774px;
        background: #fff;
        height: 1080px;
      }

      .top-content{
        width: 100%;
        position: relative;
        min-height: 250px;
      }

      .top-content .logo-container{
        text-align: center;
        position: relative;
        height: 60px;
        z-index: 9;
        /* padding-top: 12px; */
        padding-left:60px;
        padding-right:60px;
      }
      
      .top-content .logo-container img{
        height: 110px;
        width: 110px;
        /* padding: 8px; */
        /* border-radius: 50%; */
        /* background-color: #FFF; */
      }

      .top-content .header-content{
        text-align: center;
        color: #FFF !important;
        width: 100%;
        /* overflow: hidden; */
        position: relative;
        z-index: 5;
        background-color: #3E91C8;
        padding: 55px 60px 5px 60px;
        min-height: 230px;
        box-sizing: border-box;
      }

      .top-content .header-content p{
        margin: 0;
        color: #FFF !important;
      }

      .body-content{
        min-height: 725px;
        background: #FFF;
        width: 100%;
      }

      .body-content .trans-content{
        background: #F7F7F7;
        padding: 0px 60px;
        width:  100%;
        height: 100px;
      }

      .trans-content .item{
        /* display: inline-block; */
        width: 100%;
        /* height: 58px; */
        padding: 12px 0;
      }

      .trans-content .item .one{
        display:inline-block;
        width: 22px;
        height: 22px;
        margin-right: 20px;
        vertical-align: middle;
      }

      .trans-content .item .one img{
        width: 25px;
        display: inline-block;
      }

      .trans-content .item .two{
        display: inline-block;
        color: #777;
        width: 320px;
        font-size: 18px;
        vertical-align: middle;
        /* margin-right: 45px; */
      }

      .trans-content .item .three{
        display: inline-block;
        color: #333;
        font-weight: 700;
        font-size: 18px;
        vertical-align: middle;
        width: 275px;
      }

      .body-content .receipt-details{
        padding: 10px 60px;
        min-height: 100px;
        width: 100%;
      }

      .body-content .receipt-details .row{
        width: 100%;
        display: block;
      }

      .body-content .receipt-details .row .item{
        width: 275px;
        display: inline-block;
        vertical-align: top;
      }

      .body-content .receipt-details .row .one{
        /* width: 370px; */
        width: 350px;
        margin-right: 20px;
      }

      .body-content .item_service{
        padding: 12px 60px 0 60px;
        min-height: 100px;
        /* overflow: hidden; */
        width: 100%;
      }

      .billing-details-header {
        align-items: center;
        padding: 0 60px;
        display: inline-block;
      }

      .billing-details-header span {
        color: #595959;
        width: 139px;
        display: inline-block;
        font-size: 18px;
        /* margin-right: 34px; */
      }

      .billing-details-header .custom-border{
        border-bottom: 1px solid #ddd;
        width: 505px;
        display: inline-block;
        height: 15px;
      }

      .billing-details-body-container .row-grid {
        width: 100%;
        padding: 0 0 10px;
      }

      .billing-details-body-container .row-grid.last{
        border-bottom: 1px solid #ddd;
      }

      .billing-details-body-container .row-grid .title {
        font-size: 18px;
        color: #999;
        width: 370px;
        display: inline-block;
      }

      .billing-details-body-container .row-grid .amount {
        color: #333;
        font-size: 18px;
        font-weight: 700;
        display: inline-block;
        width: 275px;
      }

      .billing-details-paid {
        margin: 15px 0 0;
      }



      .contact-content{
        /* height: 150px; */
        width: 100%;
        padding: 10px 0;
      }

      .contact-content .item{
        display: inline-block;
        vertical-align: top;
        width: 175px;
      }
      
      .contact-content .item.one{
        width: 465px;
      }

      .contact-img {
        width: 35px;
        display: inline-block;
      }

      .contact-support-item {
        color: #848484;
        font-size: 18px;
        margin: 10px 0;
        width: 100%;
      }

      .contact-support-item .child-one {
        margin: 0 20px 0 0;
        height: 35px;
        width: 35px;
        display: inline-block;
        vertical-align: middle;
      }
      .contact-support-item .child-two {
        width: 320px;
        display: inline-block;
        vertical-align: middle;
      }

      
      .contact-content .item .social-img{
        display: inline-block;
        vertical-align: middle;
        margin-left: 10px;
        width: 45px;
      }
      
      .contact-content .item .social-img a img{
        width: 45px;
        display: inline-block;
      }

      
    </style>
  </head><body>
    <div id="main-template-wrapper" >
      <div class="top-content">
        <div class="logo-container">
          <img src="https://mednefits.s3-ap-southeast-1.amazonaws.com/e-template-img/new_logo_icon_pdf.png">
        </div>
        <div class="header-content">
          <p style="font-size: 20px;line-height: 29px;margin-bottom: 15px;">
            Here's the payment receipt for your visit at <span style="font-weight: 700">{{ $health_provider_name }}</span>.
          </p>
          <p style="font-size: 18px; width: 490px; margin: 0 auto 20px auto;">
            You can also view your receipts under the History section in Mednefits app.
          </p>
          <p style="font-size: 40px;font-weight: 700;line-height: 1;margin-bottom: 20px;">
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
              <div style="font-size: 18px;color: #999;margin-bottom: 5px;">
                Health Provider
              </div>
              <p style="color: #333;font-size: 18px;font-weight: 700;margin-top: 0;margin-bottom: 10px;">
                <span class="health-provider-name" style="margin: 0 0 20px">{{ $health_provider_name }}</span> 
                <br>
                {{ $health_provider_address }} {{ $health_provider_city }}, 
                {{ $health_provider_country }} {{ $health_provider_postal }}
                <br>
                {{ $health_provider_phone }}
              </p>
            </div>

            <div class="item">
              <div style="font-size: 18px;color: #999;margin-bottom: 5px;">
                Service
              </div>
              <p style="color: #333;font-size: 18px;font-weight: 700;margin-top: 0;margin-bottom: 10px;">
                {{ $service }} <br>
              </p>
            </div>
          </div>

          <div class="row">
            <div class="item one">
              <div style="font-size: 18px;color: #999;margin-bottom: 5px;">
                Member
              </div>
              <p style="color: #333;font-size: 18px;font-weight: 700;margin-top: 0;margin-bottom: 10px;">
                {{ $member }}
              </p>
            </div>

            <div class="item">
              <div style="font-size: 18px;color: #999;margin-bottom: 5px;">
                Cap Per Visit
              </div>
              <p style="color: #333;font-size: 18px;font-weight: 700;margin-top: 0;margin-bottom: 10px;">
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
              <div style="font-size: 18px;color: #777;margin-bottom: 15px;">
                Contact support
              </div>
              <div class="contact-support-item">
                <div class="child-one">
                  <img class="contact-img" src="https://mednefits.s3-ap-southeast-1.amazonaws.com/e-template-img/telephone.png">
                </div>
                <div class="child-two">
                @if($currency_symbol == "SGD")
                  <span style="text-decoration: underline;"><a href="tel:+65-3163-5403">+65 3163 5403</a></span>
                @else
                  <span style="text-decoration: underline;"><a href="tel:+65-3163-5403">+65 3163 5403</a></span>
                  <span style="margin: 0 0 0 5px;">or</span> 
                  <span style="text-decoration: underline;"><a href="tel:+603-7890-1770">+60 330 995 774</a></span>
                @endif
                </div>
              </div>
              <div class="contact-support-item">
                <div class="child-one">
                  <img class="contact-img" src="https://mednefits.s3-ap-southeast-1.amazonaws.com/e-template-img/envelope.png">
                </div>
                <div class="child-two" style="text-decoration: none;">support@mednefits.com</div>
              </div>
            </div>

            <div class="item" style="text-align: right;">
              <div style="font-size: 18px;color: #777;margin-bottom: 30px;">
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