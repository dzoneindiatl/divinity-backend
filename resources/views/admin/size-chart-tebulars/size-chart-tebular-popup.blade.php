<style>
    body {
        font-family: Arial, sans-serif;
    }

    #mesurement_type_cm {
        margin-left: 30px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }

    table span {
        font-weight: bold;
    }

    th,
    td {
        border: 1px solid black;
        padding: 5px;
        text-align: center;
    }

    th {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    .note {
        border: 1px solid black;
        padding: 5px;
        text-align: center;
        margin-top: 5px;
        background-color: #f9f9f9;
    }
</style>

<div id="tebular-size-chart">

    <h3 style="text-align: center;">{{ $size_chart_content->title ?? 'Size Chart' }}</h3>
    @if ($size_chart)
        <div>
            <input type="radio" checked="checked" id="mesurement_type_inch" name="mesurement_type" value="inch"
                onclick="changeMesurementType('inch')"> <label for="mesurement_type_inch"
                class="form-label">&nbsp;&nbsp;Inch</label>

            <input type="radio" id="mesurement_type_cm" name="mesurement_type" value="cm"
                onclick="changeMesurementType('cm')"><label for="mesurement_type_cm"
                class="form-label">&nbsp;&nbsp;CM</label>

        </div>

        @php
            $measurement_inch = [];
            $measurement_cm = [];
            if ($size_chart->measurement_inch != '') {
                $measurement_inch = json_decode($size_chart->measurement_inch, true);
            }
            if ($size_chart->measurement_cm != '') {
                $measurement_cm = json_decode($size_chart->measurement_cm, true);
            }
        @endphp
        <div class="mesurement_type_inch_div">
            <table>
                <tr>
                    <th>UPPER</th>
                    <th>XS</th>
                    <th>S</th>
                    <th>M</th>
                    <th>L</th>
                    <th>XL</th>
                    <th>2XL</th>
                </tr>
                @if (isset($measurement_inch['upper']) && is_array($measurement_inch['upper']) && count($measurement_inch['upper']) > 0)
                    @foreach ($measurement_inch['upper'] as $u_type => $inch)
                        <tr>
                            <td>{{ $u_type }}</td>
                            <td>{{ $inch['xs'] }}</td>
                            <td>{{ $inch['s'] }}</td>
                            <td>{{ $inch['m'] }}</td>
                            <td>{{ $inch['l'] }}</td>
                            <td>{{ $inch['xl'] }}</td>
                            <td>{{ $inch['2xl'] }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7">Mesurement Not Available.</td>
                    </tr>
                @endif
            </table>
            <table>
                <tr>
                    <th>BOTTOM</th>
                    <th>XS</th>
                    <th>S</th>
                    <th>M</th>
                    <th>L</th>
                    <th>XL</th>
                    <th>2XL</th>
                </tr>
                @if (isset($measurement_inch['bottom']) &&
                        is_array($measurement_inch['bottom']) &&
                        count($measurement_inch['bottom']) > 0)
                    @foreach ($measurement_inch['bottom'] as $b_type => $inch)
                        <tr>
                            <td>{{ $b_type }}</td>
                            <td>{{ $inch['xs'] }}</td>
                            <td>{{ $inch['s'] }}</td>
                            <td>{{ $inch['m'] }}</td>
                            <td>{{ $inch['l'] }}</td>
                            <td>{{ $inch['xl'] }}</td>
                            <td>{{ $inch['2xl'] }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7">Mesurement Not Available.</td>
                    </tr>
                @endif
            </table>
        </div>

        <div class="mesurement_type_cm_div" style="display: none;">
            <table>
                <tr>
                    <th>UPPER</th>
                    <th>XS</th>
                    <th>S</th>
                    <th>M</th>
                    <th>L</th>
                    <th>XL</th>
                    <th>2XL</th>
                </tr>
                @if (isset($measurement_cm['upper']) && is_array($measurement_cm['upper']) && count($measurement_cm['upper']) > 0)
                    @foreach ($measurement_cm['upper'] as $u_type => $inch)
                        <tr>
                            <td>{{ $u_type }}</td>
                            <td>{{ $inch['xs'] }}</td>
                            <td>{{ $inch['s'] }}</td>
                            <td>{{ $inch['m'] }}</td>
                            <td>{{ $inch['l'] }}</td>
                            <td>{{ $inch['xl'] }}</td>
                            <td>{{ $inch['2xl'] }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7">Mesurement Not Available.</td>
                    </tr>
                @endif
            </table>
            <table>
                <tr>
                    <th>BOTTOM</th>
                    <th>XS</th>
                    <th>S</th>
                    <th>M</th>
                    <th>L</th>
                    <th>XL</th>
                    <th>2XL</th>
                </tr>
                @if (isset($measurement_cm['bottom']) && is_array($measurement_cm['bottom']) && count($measurement_cm['bottom']) > 0)
                    @foreach ($measurement_cm['bottom'] as $b_type => $inch)
                        <tr>
                            <td>{{ $b_type }}</td>
                            <td>{{ $inch['xs'] }}</td>
                            <td>{{ $inch['s'] }}</td>
                            <td>{{ $inch['m'] }}</td>
                            <td>{{ $inch['l'] }}</td>
                            <td>{{ $inch['xl'] }}</td>
                            <td>{{ $inch['2xl'] }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7">Mesurement Not Available.</td>
                    </tr>
                @endif
            </table>
        </div>
    @else
        <div class="mesurement_type_inch_div">
            <p>Size chart not available yet. You will get soon. Thanks</p>
        </div>
    @endif


    @if ($size_chart_content)
        <div class="note">
            <p>{!! $size_chart_content->description !!}</p>
        </div>
    @endif
</div>

<script>
    function changeMesurementType(m_type) {
        if (m_type == 'inch') {
            $('.mesurement_type_cm_div').hide();
            $('.mesurement_type_inch_div').show();
        }
        if (m_type == 'cm') {
            $('.mesurement_type_inch_div').hide();
            $('.mesurement_type_cm_div').show();

        }
    }
</script>
