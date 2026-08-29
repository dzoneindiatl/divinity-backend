<table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding-top: 30px;">
    <tr>
        <td style="background:#f4edd4; display: flex; flex-direction: row;">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="padding: 80px 25px;">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="font-size: 30px; color: #c83a3a; font-weight: 500; text-align: center;">
                                    Hello Member! Have you checked our new arrivals yet?</td>
                            </tr>
                            <tr>
                                <td
                                    style="font-size: 16px; color: #070707; font-weight: 500; text-align: center; padding: 20px 10px">
                                    Don't keep waiting the latest trends just dropped in!</td>
                            </tr>
                            <tr>
                                <td>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr
                                            style="width: 100%; display: flex; justify-content: center; align-items: center;">
                                            <td style="font-size: 14px; padding-top: 10px; margin: 10px 20px;">
                                                <a href="{{ Config('constant.FRONT_WEBSITE_URL') }}" style="color:#000;font-weight:bold;"><a href="{{ Config('constant.FRONT_WEBSITE_URL') }}"
                                                        style="color:#000; line-height: 11px; text-decoration:none;  border: 1px solid #000; padding: 10px 20px;">Shop
                                                        Now</a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table class="footer-table" border="0" cellspacing="0" cellpadding="0">
    <tr style="text-align: center; align-items:center; justify-content: space-between;">
    @foreach($categories as $category)
        <td>
            <a href="{{ Config('constant.FRONT_WEBSITE_URL').'category/'.$category->slug }}" style="text-decoration: none; color: #000;">
                {{--<img src="./images/t-shirt.png" style="width: 40%;"><br>--}}
                {{ $category->name }}
            </a>
        </td>
    @endforeach
    </tr>
    <tr>
        <td colspan="5" style="color: #000;font-size: 14px;text-align: center; padding: 10px 20px; padding:30px">
            <a href="{{ Config('constant.FRONT_WEBSITE_URL').'cms/exchange-return-policy' }}" style="color: #000;">Return Policy</a> | <a href="{{ Config('constant.FRONT_WEBSITE_URL').'cms/privacy-policy' }}" style="color: #000;">Privacy Policy</a> | <a href="{{ Config('constant.FRONT_WEBSITE_URL').'cms/terms-conditions' }}" style="color: #000;">Terms & Conditions</a>
        </td>
    </tr>
    <tr>
        <td colspan="5" style="color: #000;font-size: 14px;text-align: center; padding: 10px 20px;">
            {{ Config('constant.Contact.address') }}
        </td>
    </tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>