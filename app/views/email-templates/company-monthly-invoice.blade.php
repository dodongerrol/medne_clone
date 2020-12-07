<!doctype html>
<html>
  <head>
  <meta name="viewport" content="width=device-width" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>
    Company Benefits Spending Invoice
  </title>

  <style type="text/css">
    .social-contact{
      float: left;
      margin: 20px 0 40px 0;
    }

    .social-icons{
      float: right;
      margin: 20px 0 40px 0;
    }

    @media only screen and (max-width: 485px) {
      .social-contact{
        float: none;
        display: block;
      }

      .social-icons{
        float: none;
        display: block;
      }
    }
  </style>

  </head>

  <body style="font-family:'Arial';box-sizing: border-box;margin: 0;padding: 0;display: block;">

    <div id="main-template-wrapper" style="width: 100%;height: 100%;position: relative;overflow: hidden;">

      <div style="position:relative;min-height: 500px;background: #fff;min-height: 500px;padding: 50px">
        <!-- <img src="https://medicloud.sg/e-template-img/welcome-to-mednefits-(corporate-account).jpg" style="width: 600px;"> -->
        <div style="width: 100%;text-align: center;height: 100px;border-bottom: 2px solid #ddd;margin-bottom: 50px;">
          <img src="http://admin.medicloud.sg/images/blue_logo.png" style="width: 220px;">
        </div>
        
        <p>Hi {{ $company }},</p>
        <p></p>
        <p>This is your invoice for {{ $statement_start_date }} - {{ $statement_end_date }}. Please see the attached invoice pdf.</p>
        <p>You can view the invoice under Account & Billing tab in your HR Portal.</p>
        <p style="margin: 35px 0;">If you have any queries, feel free to contact us at support@mednefits.com, we are always happy to hear from you.</p>

        <p style="font-weight: 700">Thank you</p>
        <p style="font-weight: 700;margin-bottom: 50px;">Your Mednefits Team</p>
        
        <div style="width: 100%;border-top: 2px solid #999;padding-top: 30px;">
          <div class="social-contact">
            <p style="color: #807b7b;font-weight:700;margin-top: 0;font-size: 18px;">Contact support</p>

            <p style="color: #807b7b;margin: 0;font-size: 17px;">+65 3163 5403</p>
            <p style="color: #807b7b;margin: 0;font-size: 17px;">+60 330 995 774</p>
            <p style="color: #807b7b;margin: 0;font-size: 17px;">support@mednefits.com</p>
          </div>

          <div class="social-icons" style="">
            <p style="color: #807b7b;font-weight:700;margin-top: 0;font-size: 18px;">Connect with us at</p>

            <div style="display: inline-block;">
              <img src="http://staging.medicloud.sg/e-template-img/facebook-2.png" style="width: 40px;float: left;margin-right: 20px;">
              <img src="http://staging.medicloud.sg/e-template-img/instagram-2.png" style="width: 40px;float: left;margin-right: 20px;">
              <img src="http://staging.medicloud.sg/e-template-img/linkedin-2.png" style="width: 40px;float: left;">
            </div>
          </div>
        </div>

      </div>
      
    </div>

  </body>
</html>
