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

      .billing-details-header .custom-border {
        /* width: 72%; */
        width: 400px;
      }

      @media only screen and (max-width: 768px) {
        #main-template-wrapper{
          width: 99% !important;
          margin: 0.5% auto !important;
        }
        .billing-details-header .custom-border {
          width: 65% !important;
        }
      }

      @media only screen and (max-width: 650px) {
        .header-content{
          padding: 65px 30px 0 30px !important;
        }
        .trans-content{
          padding: 0px 30px !important;
        }
        .receipt-details{
          padding: 30px 30px 10px 30px !important; 
        }
        .billing-details-header{
          padding: 0 30px !important;
        }
        .item_service{
          padding: 20px 30px !important;
        }
        .billing-details-header .custom-border {
          width: 72% !important;
        }
      }

      @media only screen and (max-width: 600px) {
        .trans-content .item{
          padding: 10px 0 !important;
        }
        .trans-content .item .one,
        .trans-content .item .two{
          font-size: 14px !important;
        }
      }
      
      @media only screen and (max-width: 550px) {
        .billing-details-header span{
          font-size: 14px !important;
          width: 105px !important;
        }
        .billing-details-header .custom-border {
          width: 65% !important;
        }
        .logo-container img{
          height: 100px !important;
          max-height: 100px !important;
          width: 100px !important;
        }
        .trans-content .item img{
          margin-right: 12px !important;
          width: 18px !important;
        }
        .header-content {
          padding: 50px 20px 0 20px !important;
        }
        .trans-content{
          padding: 0px 20px !important;
        }
        .receipt-details{
          padding: 30px 20px 10px 20px !important; 
        }
        .billing-details-header{
          padding: 0 20px !important;
        }
        .item_service{
          padding: 20px 20px !important;
        }
        .trans-content .item .one{
          width: 55% !important;
        }
        .trans-content .item .two{
          width: 42% !important;
        }
        .receipt-details .item.one{
          width: 50% !important;
        }
        .receipt-details .item div,
        .receipt-details .item p{
          font-size: 14px !important;
        }
        .billing-details-body-container .title{
          width: 55% !important;
          font-size: 14px !important;
        }
        .billing-details-body-container .amount{
          font-size: 14px !important;
        }
        .contact-content .item div{
          font-size: 14px !important;
        }
      }
      @media only screen and (max-width: 480px) {
        .billing-details-header .custom-border {
          width: 60% !important;
        }
      }
      @media only screen and (max-width: 450px) {

        .contact-content .item{
          display: block !important;
          margin-bottom: 20px !important;
          width: 100% !important;
          text-align: left !important;
        }
        .contact-content br{
          display: none !important;
        }
        .contact-content .social-img{
          margin-left: 0 !important;
          margin-right: 5px !important;
        }
      }
      @media only screen and (max-width: 400px) {
        .billing-details-header .custom-border {
          width: 55% !important;
        }
      }
      @media only screen and (max-width: 350px) {
        .billing-details-header .custom-border {
          width: 48% !important;
        }
      }
    </style>
  </head><body style="margin: 0;font-family: 'Helvetica Light',sans-serif;font-size: 16px;line-height: 1.42857143;">
    <div id="main-template-wrapper" style="margin: 10px auto 20px auto;max-width: 680px;background: #fff;min-height: 1000px;">
      <div class="top-content" style="position: relative;border: 1px solid #ccc;border-width: 1px 1px 0px 1px;box-sizing: border-box;">
        <div class="logo-container" style="text-align: center;position: relative;height: 70px;max-height: 70px;z-index: 9;padding: 12px 70px 0 70px;">
          <img style="height: 110px;max-height: 110px;width: 110px;padding: 12px;border-radius: 50%;background-color: #FFF;" src="https://mednefits.s3-ap-southeast-1.amazonaws.com/e-template-img/new_logo_icon.png">
        </div>
        <div class="header-content" style="text-align: center;color: #FFF !important;overflow: hidden;position: relative;z-index: 5;background-color: #3E91C8;padding: 65px 60px 0 60px;box-sizing: border-box;">
          <p style="font-size: 18px;line-height: 29px;margin: 0 0 15px 0;color: #FFF !important;word-break: break-word;">
            Here's the payment receipt for your visit at <span style="font-weight: 700">{{ $health_provider_name }}</span>.
          </p>
          <p style="font-size: 15px; max-width: 450px;margin: 0 auto 10px auto;color: #FFF !important;word-break: break-word;">
            You can also view your receipts under the History section in Mednefits app.
          </p>
          <p style="font-size: 35px;font-weight: 700;margin: 0 0 15px 0;color: #FFF !important;word-break: break-word;">
            Total: <span>{{ $currency_symbol }}</span> <span>{{ $credits }}</span>
          </p>

        </div>
      </div>

      <div class="body-content" style="min-height: 725px;background: #FFF;box-sizing: border-box;">
        <div class="trans-content" style="background: #F7F7F7;padding: 0px 60px;border: 1px solid #ccc;border-width: 0 1px;box-sizing: border-box;">
          <div class="item" style="border-bottom: 1px solid #ddd;display: inline-block;width: 100%;padding: 15px 0;">
            <div class="one" style="width: 60%;display:inline-block;vertical-align: middle;color: #777;font-size: 18px;">
              <img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/e-template-img/Trans-ID---Mednefits-Credits-Email.png" style="width: 26px;margin-right: 30px;vertical-align: middle;"/>
              <span style="display: inline-block;vertical-align: middle;">Transaction ID</span>
            </div>
            <div class="two" style="width: 37%;display: inline-block;vertical-align: middle;color: #333;font-weight: 700;font-size: 18px;">
              {{ $transaction_id }}
            </div>
          </div>
          <div class="item" style="display: inline-block;width: 100%;padding: 15px 0;">
            <div class="one" style="width: 60%;display:inline-block;vertical-align: middle;color: #777;font-size: 18px;">
              <img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/e-template-img/clock.png" style="width: 26px;margin-right: 30px;vertical-align: middle;"/>
              <span style="display: inline-block;vertical-align: middle;">Transaction Date</span>
            </div>
            <div class="two" style="width: 37%;display: inline-block;vertical-align: middle;color: #333;font-weight: 700;font-size: 18px;">
              {{ $transaction_date }}
            </div>
          </div>
        </div>

        <div class="receipt-details" style="padding: 30px 60px 10px 60px;min-height: 100px;border: 1px solid #ccc;border-width: 0 1px;box-sizing: border-box;">
          <div class="row" style="width: 100%;display: block;">
            <div class="item one" style="width: 55%;margin-right: 5%;display: inline-block;vertical-align: top;">
              <div style="font-size: 18px;color: #999;margin-bottom: 13px;">
                Health Provider
              </div>
              <p style="color: #333;font-size: 18px;font-weight: 700;margin-top: 0;">
                <span class="health-provider-name" style="margin: 0 0 13px">{{ $health_provider_name }}</span> 
                <br>
                {{ $health_provider_address }} {{ $health_provider_city }}, 
                {{ $health_provider_country }} {{ $health_provider_postal }}
                <br>
                {{ $health_provider_phone }}
              </p>
            </div>

            <div class="item" style="width: 36%;display: inline-block;vertical-align: top;">
              <div style="font-size: 18px;color: #999;margin-bottom: 13px;">
                Service
              </div>
              <p style="color: #333;font-size: 18px;font-weight: 700;margin-top: 0;">
                {{ $service }} <br>
              </p>
            </div>
          </div>

          <div class="row">
            <div class="item one" style="width: 55%;margin-right: 5%;display: inline-block;vertical-align: top;">
              <div style="font-size: 18px;color: #999;margin-bottom: 13px;">
                Member
              </div>
              <p style="color: #333;font-size: 18px;font-weight: 700;margin-top: 0;">
                {{ $member }}
              </p>
            </div>

            <div class="item" style="width: 36%;display: inline-block;vertical-align: top;">
              <div style="font-size: 18px;color: #999;margin-bottom: 13px;">
                Cap Per Visit
              </div>
              <p style="color: #333;font-size: 18px;font-weight: 700;margin-top: 0;">
                @if($cap_per_visit_status)
                  {{ $currency_symbol }}
                @endif

                {{ $cap_per_visit }}
              </p>
            </div>
          </div>
        </div>

        <div class="billing-details-header" style="align-items: center;padding: 0 60px;border-left: 1px solid #ccc;border-right: 1px solid #ccc;min-height: 30px;box-sizing: border-box;">
          <span style="color: #595959;width: 130px;display: inline-block;font-size: 18px;vertical-align: middle;">Billing details</span>
          <div class="custom-border" style="border-top: 1px solid #ddd;display: inline-block;vertical-align: middle;"></div>
        </div>

        <div class="item_service" style="padding: 20px 60px;min-height: 100px;overflow: hidden;border: 1px solid #ccc;border-width: 0 1px 1px 1px;box-sizing: border-box;">
          <div class="billing-details-body-container">
            <div class="row-grid" style="width: 100%;padding: 0 0 20px;">
              <div class="item title" style="width: 60%;font-size: 18px;color: #999;display: inline-block;">
                Bill Amount
              </div>
              <div class="item amount" style="width: 36%;color: #333;font-size: 18px;font-weight: 700;display: inline-block;">
                <span>{{ $currency_symbol }}</span> <span>{{ $bill_amount }}</span>
              </div>
            </div>
            <div class="row-grid" style="width: 100%;padding: 0 0 20px;">
              <div class="item title" style="width: 60%;font-size: 18px;color: #999;display: inline-block;">
                Consultation Fee
              </div>
              <div class="item amount" style="width: 36%;color: #333;font-size: 18px;font-weight: 700;display: inline-block;">
                <span>{{ $currency_symbol }}</span> <span>{{ $consultation }}</span>
              </div>
            </div>
            <div class="row-grid last" style="width: 100%;padding: 0 0 20px;border-bottom: 1px solid #ddd;">
              <div class="item title" style="width: 60%;font-size: 18px;color: #999;display: inline-block;">
                Total Amount
              </div>
              <div class="item amount" style="width: 36%;color: #333;font-size: 18px;font-weight: 700;display: inline-block;">
                <span>{{ $currency_symbol }}</span> <span>{{ $total_amount }}</span>
              </div>
            </div>
          </div>

          <div class="billing-details-body-container billing-details-paid" style="margin: 20px 0 0;">
            <div class="row-grid" style="width: 100%;padding: 0 0 20px;">
              <div class="item title" style="width: 60%;font-size: 18px;color: #999;display: inline-block;">
                Paid by Credits
              </div>
              <div class="item amount" style="width: 36%;color: #333;font-size: 18px;font-weight: 700;display: inline-block;">
                <span>{{ $currency_symbol }}</span> <span>{{$paid_by_credits}}</span>
              </div>
            </div>
            <div class="row-grid last" style="width: 100%;padding: 0 0 20px;border-bottom: 1px solid #ddd;">
              <div class="item title" style="width: 60%;font-size: 18px;color: #999;display: inline-block;">
                Paid by Cash
              </div>
              <div class="item amount" style="width: 36%;color: #333;font-size: 18px;font-weight: 700;display: inline-block;">
                <span>{{ $currency_symbol }}</span> <span>{{$paid_by_cash}}</span>
              </div>
            </div>
          </div>

          <div class="contact-content" style="width: 100%;padding: 20px 0;">
            <div class="item one" style="width: 58%;display: inline-block;vertical-align: top;">
              <div style="font-size: 18px;color: #777;margin-bottom: 20px;">
                Contact support
              </div>
              <div class="contact-support-item" style="align-items: center;color: #848484;font-size: 18px;">
                <span class="child-one" style="display: inline-block;vertical-align: middle;margin: 0 10px 0 0;height: 40px;max-height: 40px;">
                  <img class="contact-img" src="https://mednefits.s3-ap-southeast-1.amazonaws.com/e-template-img/telephone.png" style="width: 40px;">
                </span>
                @if($currency_symbol == "SGD")
                <span style="text-decoration: underline;display: inline-block;vertical-align: middle;"><a href="tel:+65-3163-5403">+65 3163 5403</a> </span>
                @else
                <span style="text-decoration: underline;display: inline-block;vertical-align: middle;"><a href="tel:+65-3163-5403">+65 3163 5403</a></span> 
                <span style="display: inline-block;vertical-align: middle;"> 
                  <span style="margin: 0 0 0 5px;">or</span> 
                  <span style="text-decoration: underline;"><a href="tel:+603-7890-1770">+60 330 995 774</a></span>
                </span>
                @endif
                
              </div>
              <br>
              <div class="contact-support-item" style="align-items: center;color: #848484;font-size: 18px;">
                <span class="child-one" style="display: inline-block;vertical-align: middle;margin: 0 10px 0 0;height: 40px;max-height: 40px;">
                  <img class="contact-img" src="https://mednefits.s3-ap-southeast-1.amazonaws.com/e-template-img/envelope.png" style="width: 40px;">
                </span>
                <span style="text-decoration: none;display: inline-block;vertical-align: middle;">support@mednefits.com</span>
              </div>
            </div>

            <div class="item" style="text-align: right;width:40%;display: inline-block;vertical-align: top;">
              <div style="font-size: 18px;color: #777;margin-bottom: 20px;">
                Connect with us at
              </div>
              <div class="social-img" style="display: inline-block;vertical-align: middle;margin-left: 5px;">
                <a href="https://www.linkedin.com/company/mednefits/" style="text-decoration: none;">
                  <img src="https://mednefits.s3-ap-southeast-1.amazonaws.com/e-template-img/linkedin.png" style="width: 40px;"/>
                </a>
              </div>
              <div class="social-img" style="display: inline-block;vertical-align: middle;margin-left: 5px;">
                <a href="https://www.instagram.com/mednefits/" style="text-decoration: none;">
                  <img src="https://mednefits.s3-ap-southeast-1.amazonaws.com/e-template-img/instagram.png" style="width: 40px;"/>
                </a>
              </div>
              <div class="social-img" style="display: inline-block;vertical-align: middle;margin-left: 5px;">
                <a href="https://www.facebook.com/Mednefits/" style="text-decoration: none;">
                  <img src="https://mednefits.s3-ap-southeast-1.amazonaws.com/e-template-img/facebook.png" style="width: 40px;"/>
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