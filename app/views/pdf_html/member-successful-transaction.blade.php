<!doctype html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width" />
    <meta name="dompdf.view" content="FitV" />
    <title>Member - Successful Transaction</title>
  </head>
  <style>
    @page {
     margin: 5px;
     width: 100%;
    }
    body
    {
      width: 100%;
      background-color: #f1f1f1;
    }
    .page-break {
      page-break-after: always;
    }
    #main-template-wrapper{
      background-color: #f1f1f1;
      width: 100%;
      height: 100%;
      position: relative;
      overflow: hidden;
    }

    #main-template-wrapper .temp-container{
      margin: 20px auto 70px auto;
      width: 100%;
      min-height: 500px;
      background: #fff;
      min-height: 1000px;
    }

    .top-content{
      height: 350px;
      background-image: url('http://staging.medicloud.sg/e-template-img/email-logo.png');
      background-size: 100%;
      background-repeat: no-repeat;
      position: relative;
      display: inline-block;
      width: 100%;
    }

    .top-content .header-content{
      text-align: center;
      margin-top: 130px;
      color: #FFF !important;
      padding: 0 10px;
    }

    .clinic-type-img{
      float: left;
    } 

    .clinic-type-service{
      float: left;
      font-weight: 700;
      margin: 5px 25px;
      font-size: 16px;
    }

    .clinic-type-credits{
      float:right;
      text-align: right;
      font-size: 16px;
      font-weight: 700;
      margin: 5px 0;
    }

    .clinic-type-total{
      height: 115px;
      border-bottom: 2px solid #ddd;
      margin-top: 20px;
      margin-bottom: 20px;
      text-align: right;
    }

    .item_service{
      padding: 60px 40px;
      min-height: 100px;
    }

    @media only screen and (max-width: 670px) {
      #main-template-wrapper .temp-container{
        min-width: unset;
        width: 100%;
      }
    }

    @media only screen and (max-width: 550px) {
      .top-content{
        height: 320px;
      }
      
      .top-content .header-content{
        margin-top: 105px;
      }
    }

    @media only screen and (max-width: 490px) {
      .item_service{
        padding-top: 20px;
      }

      .clinic-type-img{
        float: none;
        text-align: center;
        margin-bottom: 20px;
      } 

      .clinic-type-service{
        float: none;
        text-align: center;
        margin-bottom: 20px;
      }

      .clinic-type-credits{
        float:none;
        text-align: center;
      }

      .clinic-type-total{
        text-align: center;
      }
    }

    @media only screen and (max-width: 454px) {
      
      .top-content .header-content{
        margin-top: 85px;
      }
    }

  </style>

  <body style="font-family:'Arial';box-sizing: border-box;margin: 0;padding: 0;display: block;">

    <div id="main-template-wrapper">
      <div class="temp-container">
        <div class="top-content">

          <div class="header-content">
            <p style="font-size: 24px;color: #FFF !important;">
              Hello, <span>{{ $member }}</span>
            </p>
            <p style="font-size: 22px;color: #FFF !important;">
              Hope you had a great healthcare experience
            </p>
            <p style="margin-top: 35px;margin-bottom: 5px;font-size: 18px;color: #FFF !important;">
              Your Receipt
            </p>
            <p style="font-size: 44px;font-weight: 700;margin-top: 0;color: #FFF !important;">
              S$ <span>{{ $credits }}</span>
            </p>
          </div>
        </div>

        <div style="border-left: 1px solid #999;border-right: 1px solid #999;border-bottom: 1px solid #999;min-height: 700px;">
          <div style="background: #F7F7F7;overflow: hidden;padding: 30px 40px;">
            <div style="border-bottom: 1px solid #ddd;padding-bottom: 10px;margin-bottom: 10px;">
              <div style="float: left;margin-right: 15px;">
                <img src="http://staging.medicloud.sg/e-template-img/Trans-ID---Mednefits-Credits-Email.png" style="width: 20px;" />
              </div>
              <div style="display: inline-block;color: #777;width: 190px;font-size: 15px;">
                Transaction ID
              </div>
              <div style="display: inline-block;color: #333;font-weight: 700;font-size: 17px;">
                {{ $transaction_id }}
              </div>
            </div>

            <div style="padding-bottom: 10px;margin-top: 10px;">
              <div style="float: left;margin-right: 15px;">
                <img src="http://staging.medicloud.sg/e-template-img/clock.png" style="width: 20px;" />
              </div>
              <div style="display: inline-block;color: #777;width: 190px;font-size: 15px;">
                Transaction Date
              </div>
              <div style="display: inline-block;color: #333;font-weight: 700;font-size: 17px;">
                {{ $transaction_date }}
              </div>
            </div>
          </div>

          <div style="padding: 30px 40px;min-height: 100px;overflow: hidden;">
            <div style="width: 310px;float: left;">
              <div style="font-size: 15px;color: #999;">
                Payment Type
              </div>
              <p style="color: #333;font-size: 17px;font-weight: 700;">
                Mednefits Credits
              </p>

              <div style="margin-bottom: 40px;"></div>

              <div style="font-size: 15px;color: #999;">
                Health Provider
              </div>
              <p style="color: #333;font-size: 17px;font-weight: 700;">
                {{ $health_provider_name }}
              </p>
            </div>

            <div style="width: 200px;float: left;">
              <div style="font-size: 15px;color: #999;">
                Member
              </div>
              <p style="color: #333;font-size: 17px;font-weight: 700;">
                {{ $member }}
              </p>

              <div style="margin-bottom: 40px;"></div>

              <div style="font-size: 15px;color: #999;">
                Health Provider Contact
              </div>
              <p style="color: #333;font-size: 17px;font-weight: 700;">
                {{ $health_provider_address }} {{ $health_provider_city }}, {{ $health_provider_country }}
                <br>
                {{ $health_provider_phone }}
              </p>
            </div>
          </div>
          <div class="page-break"></div>
          <div style="position: relative;overflow: hidden;">
            <img src="http://staging.medicloud.sg/e-template-img/email-item-header.png" />
          </div>

          <div class="item_service">
            <div style="margin-bottom: 30px;min-height: 54px;">
              <div class="clinic-type-img" style="">
                <img src="{{$clinic_type_image}}" style="width: 50px;" />
              </div>
              <div class="clinic-type-service" style="">
                {{ $service }}
              </div>
              <div class="clinic-type-credits" style="">
                S$ <span>{{ $credits }}</span>
              </div>
            </div>

            <div class="clinic-type-total" style="">
              <div style="display: inline-block;color: #aaa;width: 50px;margin-right: 20px;font-size: 14px;">
                Total
              </div>
              <div style="font-size: 20px;color: #333;font-weight: 700;margin: 5px 0;font-size: 20px;display: inline-block;">
                S$ <span>{{ $credits }}</span>
              </div>
            </div>

            <div style="overflow: hidden;">
              <div style="width: 375px;float: left;">
                <div style="font-size: 15px;color: #777;font-weight: 700;margin-top: 15px;margin-bottom: 20px;">
                  Contact support
                </div>
                <p style="color: #999;font-size: 13px;">
                  +65 3163 5403
                  <br>
                  <span style="text-decoration: underline;">support@mednefits.com</span>
                </p>
              </div>

              <div style="width: 180px;float: left;">
                <div style="font-size: 15px;color: #777;font-weight: 700;margin-top: 15px;margin-bottom: 20px;">
                  Connect with us at
                </div>
                <div style="float: left;">
                  <img src="http://staging.medicloud.sg/e-template-img/facebook-2.png" style="width: 40px;margin-right: 20px;" />
                </div>
                <div style="float: left;">
                  <img src="http://staging.medicloud.sg/e-template-img/instagram-2.png" style="width: 40px;margin-right: 20px;" />
                </div>
                <div style="float: left;">
                  <img src="http://staging.medicloud.sg/e-template-img/linkedin-2.png" style="width: 40px;margin-right: 20px;" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
    </div>

  </body>
</html>
