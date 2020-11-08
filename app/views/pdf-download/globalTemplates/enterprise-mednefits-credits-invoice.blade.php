<!DOCTYPE html>
<html><head>
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Employee Plan Invoice</title>
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
        font-size: 14px;
        line-height: 1.42857143;
      }

      #main-template-wrapper{
        border: 1px solid #ccc;
        width: 100%;
        max-width: 774px;
        background: #fff;
        height: 1080px;
        margin: 0 auto;
        /* padding: 20px 0; */
      }

      
    </style>
  </head><body>
    <div id="main-template-wrapper" >
      <table border="0" cellpadding="0" cellspacing="0" style="margin: 0; padding: 0" width="100%">
        <tr>
          <td align="right" valign="top" colspan="2" style="padding: 50px 40px 50px 0px;">
            <img src="https://mednefits.s3-ap-southeast-1.amazonaws.com/images/mobile-logo-blue-latest.png" style="width: 270px;">
          </td>
        </tr>
        <tr>
          <td style="width: 60%;padding-left: 40px;padding-bottom: 80px;">
            <p style="font-size: 35px;line-height: 35px;margin: 0 0 15px 0;">INVOICE</p>
            <p style="font-size: 14px;line-height: 14px;margin: 0 0 10px 0;">{{$company}}</p>
            <p style="font-size: 14px;line-height: 14px;margin: 0 0 10px 0;">Attention: {{$name}}</p>
            <p style="font-size: 14px;line-height: 14px;margin: 0 0 10px 0;">{{$address}}</p>
            <p style="font-size: 14px;line-height: 14px;margin: 0 0 10px 0;">{{$postal}}</p>
            <p style="font-size: 14px;line-height: 14px;margin: 0 0 10px 0;text-transform: uppercase;">{{$currency_type == "SGD" ? "Singapore" : "Malaysia"}}</p>
          </td>
          <td style="vertical-align: top;padding-right: 40px;">
            <div class="invoice-number-address" style="width: 100%;display: inline-block;vertical-align: top;">
              <div class="one" style="width: 46%;display: inline-block;vertical-align: top;">
                <p style="font-weight: 700;font-size: 14px;line-height: 14px;margin: 0 0 10px 0;">Invoice Date</p>
                <p style="font-size: 14px;line-height: 14px;margin: 0 0 20px 0;">{{$invoice_date}}</p>
    
                <p style="font-weight: 700;font-size: 14px;line-height: 14px;margin: 0 0 10px 0;">Invoice Number</p>
                <p style="font-size: 14px;line-height: 14px;margin: 0 0 20px 0;">{{$invoice_number}}</p>
              </div>
              <div class="two" style="width: 52%;display: inline-block;vertical-align: top;">
                <p style="font-size: 14px;line-height: 16px;margin: 0;">
                  @if($currency_type == "SGD")
                   <!-- IF SINGAPORE -->
                    <span>
                    Medicloud Pte Ltd<br>
                    7 Temasek Boulevard<br>
                    #18-02 Suntec Tower One<br>
                    038987<br>
                    Singapore
                    </span>
                  @else
                    <span>
                      Mednefits Sdn Bhd<br>
                      Komune, Level 2,<br>
                      No. 20, Jalan Kerinchi Kiri 3,<br>
                      59200, Kuala Lumpur,<br>
                      Malaysia<br>
                    </span>
                  @endif
                </p>
              </div>
            </div>
          </td>
        </tr>
      </table>

      <table border="0" cellpadding="0" cellspacing="0" style="margin: 0 0 80px 0; padding: 0 40px;" width="100%">
        <tr>
          <td style="width: 40%;font-size: 14px;border-bottom: 2px solid #000;padding-bottom: 5px;font-weight: 700;">Description</td>
          <td style="width: 10%;font-size: 14px;border-bottom: 2px solid #000;padding-bottom: 5px;font-weight: 700;text-align: right;">Quantity</td>
          <td style="width: 15%;font-size: 14px;border-bottom: 2px solid #000;padding-bottom: 5px;font-weight: 700;text-align: right;">Unit Price</td>
          <td style="width: 12%;font-size: 14px;border-bottom: 2px solid #000;padding-bottom: 5px;font-weight: 700;text-align: right;">Tax</td>
          <td style="width: 18%;font-size: 14px;border-bottom: 2px solid #000;padding-bottom: 5px;font-weight: 700;text-align: right;">Amount {{$currency_type}}</td>
        </tr>
        <tr>
          <td style="padding: 20px 0 5px 0;">
            <p style="font-size: 14px;line-height: 14px;margin: 0;">{{$plan_type}}</p>
          </td>
          <td colspan="4"></td>
        </tr>
        <tr>
          <td style="padding: 5px 0;">
            <p style="font-size: 14px;line-height: 14px;margin: 0;">Active type: Enterprise Plan</p>
          </td>
          <td colspan="4"></td>
        </tr>
        <tr>
          <td style="padding: 5px 0;" >
            <p style="font-size: 14px;line-height: 14px;margin: 0;">Number of Employee Accounts:</p>
          </td>
          <td style="text-align: right;">
            {{$number_employess}}
          </td>
          <td style="text-align: right;">
            {{$price}}
          </td>
          <td style="text-align: right;">
            No Tax
          </td>
          <td style="text-align: right;">
            {{$amount}}
          </td>
        </tr>
        <tr>
          <td style="padding: 5px 0;">
            <p style="font-size: 14px;line-height: 14px;margin: 0;">Billing Frequency: Annual</p>
          </td>
          <td colspan="4"></td>
        </tr>

        <tr>
          <td style="padding: 5px 0;">
            <p style="font-size: 14px;line-height: 14px;margin: 0;">Start Date: {{$plan_start}}</p>
          </td>
          <td colspan="4"></td>
        </tr>
        <tr>
          <td style="border-bottom: 1px solid #BFBFBF;padding: 5px 0;" >
            <p style="font-size: 14px;line-height: 14px;margin: 0;">End Date: {{$plan_end}}</p>
          </td>
          <td colspan="4" style="border-bottom: 1px solid #BFBFBF;"></td>
        </tr>
      

        <tr>
          <td></td>
          <td colspan="3" style="text-align: right;border-bottom: 2px solid #000;padding: 10px 0;">
            Subtotal
          </td>
          <td style="text-align: right;border-bottom: 2px solid #000;padding: 10px 0;">
            {{$amount_due}}
          </td>
        </tr>
        <tr>
          <td></td>
          <td colspan="3" style="text-align: right;padding: 10px 0;">
            TOTAL {{$currency_type}}
          </td>
          <td style="text-align: right;font-weight: 700;padding: 10px 0;">
            {{$total}}
          </td>
        </tr>

        <tr>
          <td></td>
          <td colspan="3" style="text-align: right;border-bottom: 2px solid #000;padding: 10px 0;">
            Less Amount Paid<br>
            (via Bank Transfer)
          </td>
          <td style="text-align: right;border-bottom: 2px solid #000;padding: 10px 0;">
            {{$statement_amount_due}}
          </td>
        </tr>
        <tr>
          <td colspan="4" style="text-align: right;padding: 10px 0;">
            AMOUNT DUE {{$currency_type}}
          </td>
          <td style="text-align: right;font-weight: 700;padding: 10px 0;">
            {{$statement_total_amount}}
          </td>
        </tr>
      </table>

      <table border="0" cellpadding="0" cellspacing="0" style="margin: 0; padding: 0 40px;" width="100%">
        <tr>
          <td>
            <p style="font-weight: 700;font-size: 18px;line-height: 16px;margin: 0 0 10px 0;">Due Date: {{$invoice_due}}</p>
            <p style="font-size: 14px;line-height: 14px;margin: 0 0 35px 0;">Payment Information:</p>

            <p style="font-size: 14px;line-height: 14px;margin: 0 0 10px 0;">Bank Transfer:</p>
            <p style="font-size: 14px;line-height: 14px;margin: 0 0 10px 0;">Bank: UOB</p>
            @if($currency_type == "SGD")
            <!-- IF SINGAPORE -->
            <p style="font-size: 14px;line-height: 14px;margin: 0 0 10px 0;">Account Name: Medicloud Pte Ltd</p>
            <p style="font-size: 14px;line-height: 14px;margin: 0 0 25px 0;">Account Number: 3743069399</p>
            @else
            <p style="font-size: 14px;line-height: 14px;margin: 0 0 10px 0;">Account Name: Mednefits Sdn. Bhd.</p>
            <p style="font-size: 14px;line-height: 14px;margin: 0 0 25px 0;">Account Number: 2213020031</p>
            @endif
            <p style="font-weight: 700;font-size: 14px;line-height: 14px;margin: 0 0 10px 0;">Note: Please quote invoice number when submitting payment</p>
          </td>
        </tr>
      </table>

    </div>
    </body></html>
