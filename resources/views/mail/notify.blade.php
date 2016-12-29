<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body style="background: #FAFAFA; color: #333333;">
<center>
    <table border="0" cellpadding="20" cellspacing="0" height="100%" width="600" style="background: #ffffff; border: 1px solid #DDDDDD;">
        <tbody>
        <tr>
            <td style="background: #079fff; padding: 10px 10px;">
                <div class="logo" height="60" width="60" style="display: inline-block; vertical-align: middle; margin-right: 25px;">
                    <a href="index.html" style="display: block;"><img src="{{$message->embed(public_path() .'/img/logo.jpg')}}" alt="Logo" style="display: block;"></a>
                    {{--<a href="index.html" style="display: block;"><img src="/img/logo.jpg" alt="Logo" style="display: block;"></a>--}}
                </div>
            </td>
        </tr>
        <tr>
            <td style="padding: 3px 10px;">
                <div style="height: 40px; line-height: 40px; padding: 0 15px; background: #079fff; color: #ffffff; font-size: 15px; font-weight: bold; font-style: normal; font-family: proxima_nova_rgregular, Arial, Helvetica, sans-serif;">Status change for Order #{{$order->id}}</div>
            </td>
        </tr>
        <tr>
            <td style="padding: 3px 10px;">
                <div style="height: 40px; line-height: 40px; padding: 0 15px; background: #079fff; color: #ffffff; font-size: 15px; font-weight: bold; font-style: normal; font-family: proxima_nova_rgregular, Arial, Helvetica, sans-serif;">New status is "{{$order->status}}"</div>
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 10px;">
                <div style="line-height: 40px; padding: 0 15px; background: #079fff; color: #ffffff; font-size: 15px; font-weight: bold; font-style: normal; font-family: proxima_nova_rgregular, Arial, Helvetica, sans-serif;">Number:  "{{$order->phone->phone}}" is assigned to SIM #: "{{$order->sim->number}}"</div>
            </td>
        </tr>
        <tr>
            <td style="font-size: 0; padding: 10px 10px;">
                <div class="email_dates" style="display: inline-block; vertical-align: middle; width: 48%;">
                    <div class="departure" height="30" style="height: 30px; line-height: 30px; background: #079fff; color: #ffffff; text-align: center; font-size: 15px; font-weight: normal; font-style: normal; font-family: proxima_nova_rgregular, Arial, Helvetica, sans-serif;">
                        From</div>

                    <div class="email_date_time" style="margin-top: 15px; color: #079fff; text-align: center; font-size: 15px; font-weight: normal; font-style: normal;">{{$order->landing}} </div>

                </div>
                <div class="email_dates" style="display: inline-block; vertical-align: middle; width: 48%; margin-left: 4%;">
                    <div class="departure" width="45%" height="30" style="height: 30px; line-height: 30px; background: #079fff; color: #ffffff; text-align: center; font-size: 15px; font-weight: normal; font-style: normal; font-family: proxima_nova_rgregular, Arial, Helvetica, sans-serif;"> To</div>

                    <div class="email_date_time" style="margin-top: 15px; color: #079fff; text-align: center; font-size: 15px; font-weight: normal; font-style: normal;">{{$order->departure}}</div>
                </div>
            </td>
        </tr>
        <tr>
            <td style="padding: 3px 10px;">
                <div style="height: 40px; line-height: 40px; padding: 0 15px; background: #079fff; color: #ffffff; font-size: 15px; font-weight: bold; font-style: normal; font-family: proxima_nova_rgregular, Arial, Helvetica, sans-serif;">The Order is </div>
            </td>
        </tr>
        <tr>
            <td style="font-size: 0; padding: 10px 10px;">
                <div class="email_dates" style="display: inline-block; vertical-align: middle; width: 48%;">
                    <div class="departure" height="30" style="height: 30px; line-height: 30px; background: #079fff; color: #ffffff; text-align: center; font-size: 15px; font-weight: normal; font-style: normal; font-family: proxima_nova_rgregular, Arial, Helvetica, sans-serif;">
                        Created by</div>

                    <div class="email_date_time" style="margin-top: 15px; color: #079fff; text-align: center; font-size: 15px; font-weight: normal; font-style: normal;">{{$order->creator->name}} </div>

                </div>
                <div class="email_dates" style="display: inline-block; vertical-align: middle; width: 48%; margin-left: 4%;">
                    <div class="departure" width="45%" height="30" style="height: 30px; line-height: 30px; background: #079fff; color: #ffffff; text-align: center; font-size: 15px; font-weight: normal; font-style: normal; font-family: proxima_nova_rgregular, Arial, Helvetica, sans-serif;"> At</div>

                    <div class="email_date_time" style="margin-top: 15px; color: #079fff; text-align: center; font-size: 15px; font-weight: normal; font-style: normal;">{{$order->created_at}}</div>
                </div>
            </td>
        </tr>
        <tr>
            <td style="font-size: 0; padding: 10px 10px;">
                <div class="email_dates" style="display: inline-block; vertical-align: middle; width: 48%;">
                    <div class="departure" height="30" style="height: 30px; line-height: 30px; background: #079fff; color: #ffffff; text-align: center; font-size: 15px; font-weight: normal; font-style: normal; font-family: proxima_nova_rgregular, Arial, Helvetica, sans-serif;">
                       Updated by</div>

                    <div class="email_date_time" style="margin-top: 15px; color: #079fff; text-align: center; font-size: 15px; font-weight: normal; font-style: normal;">{{$order->editor->name}}</div>

                </div>
                <div class="email_dates" style="display: inline-block; vertical-align: middle; width: 48%; margin-left: 4%;">
                    <div class="departure" width="45%" height="30" style="height: 30px; line-height: 30px; background: #079fff; color: #ffffff; text-align: center; font-size: 15px; font-weight: normal; font-style: normal; font-family: proxima_nova_rgregular, Arial, Helvetica, sans-serif;"> At</div>

                    <div class="email_date_time" style="margin-top: 15px; color: #079fff; text-align: center; font-size: 15px; font-weight: normal; font-style: normal;">{{$order->updated_at}}</div>
                </div>
            </td>
        </tr>
        <tr>
            <td style="padding: 3px 20px;">
                {{--<p>text</p>--}}
            </td>
        </tr>
        <tr>
            <td>
                <br />SYC GROUP
                <br /> Phone: +(972)-52-890-7711
                <br /> Email: service@syc.co.il </td>
        </tr>
        </tbody>
    </table>
</center>
</body>
</html>