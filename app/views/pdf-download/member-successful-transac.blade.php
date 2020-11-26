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
        margin: 0;
        font-family: 'Helvetica Light',sans-serif;
        font-size: 16px;
        line-height: 1.42857143;
      }

      #main-template-wrapper{
        margin: 10px auto 20px auto;
        width: 700px;
        background: #fff;
        min-height: 1000px;
      }

      .top-content{
        /*height: 300px;*/
        width: 100%;
        position: relative;
        overflow: hidden;
      }

      .top-content .header-content{
        text-align: center;
        margin-top: 90px;
        color: #FFF !important;
        width: 100%;
        overflow: hidden;
        position: relative;
        z-index: 99;
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
      }

      .body-content .trans-content{
        background: #F7F7F7;
        overflow: hidden;
        padding: 5px 40px;
        width:  100%;
        border-left: 1px solid #ccc;
        border-right: 1px solid #ccc;
      }

      .trans-content .item{
        display: inline-block;
        width: 100%;
        height: 25px;
        margin: 10px 0 !important;
      }

      .trans-content .item .one{
        display:inline-block;
        width:30px;
        margin-right: 15px;
        vertical-align: top;
      }

      .trans-content .item .two{
        display: inline-block;
        color: #777;
        width: 255px;
        font-size: 15px;
      }

      .trans-content .item .three{
        display: inline-block;
        color: #333;
        font-weight: 700;
        font-size: 17px;
      }

      .body-content .receipt-details{
        /*padding: 10px 40px 0px 40px;*/
        padding: 20px 40px 0px 40px;
        min-height: 100px;
        overflow: hidden;
        width: 100%;
        border-left: 1px solid #ccc;
        border-right: 1px solid #ccc;
      }

      .body-content .receipt-details .row{
        width: 100%;
        display: block;
      }

      .body-content .receipt-details .row .item{
        width: 200px;
        display: inline-block;
        vertical-align: top;
      }

      .body-content .item-img{
        overflow: hidden;
        width: 100%;
        border-left: 1px solid #ccc;
        border-right: 1px solid #ccc;
        padding: 0 40px;
      }

      .body-content .item_service{
        padding: 20px 40px;
        min-height: 100px;
        overflow: hidden;
        width: 100%;
        border-left: 1px solid #ccc;
        border-right: 1px solid #ccc;
        border-bottom: 1px solid #ccc;
      }

      .body-content .item_service .item-clinic-img{
        margin-bottom: 30px;
        min-height: 54px;
      }

      .clinic-type-img{
        display: inline-block;
        width: 60px;
        vertical-align: middle;
      } 

      .clinic-type-service{
        display: inline-block;
        width: 50%;
        font-weight: 700;
        margin: 5px 25px;
        font-size: 16px;
      }

      .clinic-type-credits{
        /*float:right;*/
        display: inline-block;
        width: 30%;
        text-align: right;
        font-size: 16px;
        font-weight: 700;
        margin: 5px 0;
      }

      .clinic-type-total{
        height: 75px;
        width: 100%;
        border-bottom: 2px solid #ddd;
        margin-top: 20px;
        margin-bottom: 20px;
      }

      .clinic-type-total .one{
        display: inline-block;
        color: #aaa;
        width: 75%;
        /*margin-right: 20px;*/
        font-size: 14px;
        text-align: right;
        
      }

      .clinic-type-total .two{
        font-size: 20px;
        color: #333;
        font-weight: 700;
        font-size: 20px;
        display: inline-block;
        width: 23%;
        text-align: right;
      }

      .contact-content{
        height: 150px;
        overflow: hidden;
        width: 100%;
      }

      .contact-content .item{
        width: 60%;
        display: inline-block;
        /*vertical-align: middle;*/
        vertical-align: top;
      }

      .contact-content .item .social-img{
        width: 40px;
        display: inline-block;
        vertical-align: middle;
        margin-right: 20px;
      }
      .billing-details-header {
        display: flex;
        align-items: center;
      }
      .billing-details-header span {
        color: #595959;
        flex: 1;
      }
      .billing-details-header .custom-border{
        border: 1px solid #ddd;
        content: "";
        display: block;
        width: 80%;
      }
      .body-content .receipt-details .row-grid,
      .billing-details-body-container .row-grid {
        display: grid;
        grid-template-columns: 50% 50%;
      }
      .billing-details-body-container .row-grid {
        padding: 0 0 20px;
      }
      .billing-details-body-container .row-grid:last-child {
        border-bottom: 1px solid #ddd;
      }
      .billing-details-body-container .row-grid .title {
        font-size: 15px;
        color: #999;
      }
      .billing-details-body-container .row-grid .amount {
        color: #333;
        font-size: 15px;
        font-weight: 700;
      }
      .billing-details-paid {
        margin: 20px 0 0;
      }
      .receipt-details .row-grid >.item >div {
        margin: 0 0 10px;
      }
      .body-content .receipt-details .row-grid >.item {
        width: auto;
      }
      .contact-img {
        width: 25px;
      }
      .contact-support-item {
        display: flex;
        align-items: center;
        color: #848484;
      }
      .contact-support-item >span:first-child {
        margin: 0 20px 0 0;
      }
    </style>
  </head><body>
    <div id="main-template-wrapper" style="background-image: url('https://s3-ap-southeast-1.amazonaws.com/mednefits/e-template-img/email-pdf-logo.png');background-size: 100%;">
      <div class="top-content">
        <div class="header-content">
          <p style="font-size: 16px;margin: 30px 0 10px;letter-spacing:1.1px;">
            <!-- Hello, <span>{{ $member }}</span> -->
            Here's the payment receipt for your visit at <span style="font-weight: 700">Medicloud Family Clinic</span>.
          </p>
          <!-- <p style="font-size: 22px;">
            Hope you had a great healthcare experience
          </p> -->
          <!-- <p style="margin-top: 20px;margin-bottom: 5px;font-size: 18px;">
            Your Receipt
          </p> -->
          <p style="font-size: 15px; width: 390px; margin: 0 auto;">
            You can also view your receipts under the History section in Mednefits app.
          </p>
          <p style="font-size: 23px;font-weight: 700;margin: 20px 0;">
            Total: <span>SGD</span> <span>{{ $credits }}</span>
          </p>

        </div>
      </div>

      <div class="body-content">
        <div class="trans-content">
          <div class="item" style="margin-bottom: 0px !important;border-bottom: 1px solid #ddd;height: 30px;">
            <div class="one">
              <img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/e-template-img/Trans-ID---Mednefits-Credits-Email.png" style="width: 20px;" />
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
              <img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/e-template-img/clock.png" style="width: 20px;" />
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
          <div class="row row-grid">
            <div class="item">
              <!-- <div style="font-size: 15px;color: #999;">
                Payment Type
              </div>
              <p style="color: #333;font-size: 17px;font-weight: 700;margin-top: 0;">
                {{ $transaction_type }}
              </p> -->
              <div style="font-size: 15px;color: #999;">
                Health Provider
              </div>
              <p style="color: #333;font-size: 15px;font-weight: 700;margin-top: 0;">
                <div class="health-provider-name" style="margin: 0 0 10px">{{ $health_provider_name }}</div> 
                {{ $health_provider_address }} {{ $health_provider_city }}, {{ $health_provider_country }}
                <br>
                {{ $health_provider_phone }}
              </p>
            </div>

            <div class="item">
              <!-- <div style="font-size: 15px;color: #999;">
                Member
              </div>
              <p style="color: #333;font-size: 17px;font-weight: 700;margin-top: 0;">
                {{ $member }}
              </p> -->
              <div style="font-size: 15px;color: #999;">
                Service
              </div>
              <p style="color: #333;font-size: 15px;font-weight: 700;margin-top: 0;">
                {{ $health_provider_name }} <br>
              </p>
            </div>
          </div>

          <div class="row row-grid">
            <div class="item">
              <!-- <div style="font-size: 15px;color: #999;">
                Health Provider
              </div>
              <p style="color: #333;font-size: 17px;font-weight: 700;margin: 0;">
                {{ $health_provider_name }}
              </p> -->
              <div style="font-size: 15px;color: #999;">
                Member
              </div>
              <p style="color: #333;font-size: 17px;font-weight: 700;margin-top: 0;">
                {{ $member }}
              </p>
            </div>

            <div class="item">
              <!-- <div style="font-size: 15px;color: #999;">
                Health Provider Contact
              </div>
              <p style="color: #333;font-size: 17px;font-weight: 700;margin: 0;">
                {{ $health_provider_address }} {{ $health_provider_city }}, {{ $health_provider_country }}
                <br>
                {{ $health_provider_phone }}
              </p> -->
              <div style="font-size: 15px;color: #999;">
                Cap Per Visit
              </div>
              <p style="color: #333;font-size: 17px;font-weight: 700;margin-top: 0;">
                {{ $member }}
              </p>
            </div>
          </div>
        </div>

        <div class="item-img billing-details-header">
          <!-- <img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/e-template-img/email-item-header.png" style="width: 100%;"/> -->
          <span style="color:#595959">Billing details</span>
          <div class="custom-border"></div>
        </div>

        <div class="item_service">
          <div class="billing-details-body-container">
            <div class="row-grid">
              <div class="item title">
                Bill Amount
              </div>
              <div class="item amount">
                <span>SGD</span> <span>{{ $credits }}</span>
              </div>
            </div>
            <div class="row-grid">
              <div class="item title">
                Consultation Fee
              </div>
              <div class="item amount">
                <span>SGD</span> <span>{{ $consultation }}</span>
              </div>
            </div>
            <div class="row-grid">
              <div class="item title">
                Total Amount
              </div>
              <div class="item amount">
                <span>SGD</span> <span>{{ $total_amount }}</span>
              </div>
            </div>
          </div>

          <div class="billing-details-body-container billing-details-paid">
            <div class="row-grid">
              <div class="item title">
                Paid by Credits
              </div>
              <div class="item amount">
                <span>SGD</span> <span>{{$paid_by_credits}}</span>
              </div>
            </div>
            <div class="row-grid">
              <div class="item title">
                Paid by Cash
              </div>
              <div class="item amount">
                <span>SGD</span> <span>{{$paid_by_cash}}</span>
              </div>
            </div>
          </div>

          <div class="contact-content">
            <div class="item">
              <div style="font-size: 15px;color: #777;font-weight: 700;margin-top: 15px;margin-bottom: 20px;">
                Contact support
              </div>
              <p style="color: #999;font-size: 15px;">
                <div class="contact-support-item">
                  <span>
                    <a href=""><img class="contact-img" src="https://mednefits.s3-ap-southeast-1.amazonaws.com/e-template-img/telephone.png"></a>
                  </span>
                  <span style="text-decoration: underline;">+65 3163 5403 </span> <span> <span style="margin: 0 0 0 5px;">or</span> <span style="text-decoration: underline;">+60 330 995 774</span></span>
                </div>
                <br>
                <div class="contact-support-item">
                  <span>
                    <a href=""><img class="contact-img" src="https://mednefits.s3-ap-southeast-1.amazonaws.com/e-template-img/envelope.png"></a>
                  </span>
                  <span style="text-decoration: none; font-size: 15px;">support@mednefits.com</span>
                </div>
              </p>
            </div>

            <div class="item" style="width: 190px">
              <div style="font-size: 15px;color: #777;font-weight: 700;margin-top: 15px;margin-bottom: 20px;">
                Connect with us at
              </div>
              <div class="social-img">
                <a href="https://www.linkedin.com/company/mednefits/" style="text-decoration: none;">
                  <img src="https://mednefits.s3-ap-southeast-1.amazonaws.com/e-template-img/linkedin.png" style="width: 40px;margin-right: 20px;" />
                </a>
              </div>
              <div class="social-img">
                <a href="https://www.instagram.com/mednefits/" style="text-decoration: none;">
                  <img src="https://mednefits.s3-ap-southeast-1.amazonaws.com/e-template-img/instagram.png" style="width: 40px;margin-right: 20px;" />
                </a>
              </div>
              <div class="social-img">
                <a href="https://www.facebook.com/Mednefits/" style="text-decoration: none;">
                  <img src="https://mednefits.s3-ap-southeast-1.amazonaws.com/e-template-img/facebook.png" style="width: 40px;margin-right: 20px;" />
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