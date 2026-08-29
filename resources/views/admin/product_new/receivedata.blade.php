<?php
//echo "<pre>";print_r($vaientvaluedata);
$globalarray = array();

$variantNames = !empty($formdata['varientname'])?array_values($formdata['varientname']):'';
$optionNames = !empty($formdata['optionname'])?$formdata['optionname']:array();

function array_cartesian_product($arrays)
{
    $result = [[]];
    if(count($arrays) > 0)
    {
        foreach ($arrays as $property => $propertyValues) {
            $temp = [];
            foreach ($result as $resultItem) {
                foreach ($propertyValues as $propertyValue) {
                    if (!empty($propertyValue)) {
                        $temp[] = array_merge($resultItem, [$property => $propertyValue]);
                    }
                }
            }
            $result = $temp;
        }
    }
    return $result;
}

// Generate all possible combinations of attributes
$configurations = array_cartesian_product($optionNames);

// Display the configurations with attribute names and values
$attributearray = array();
foreach ($configurations as $configuration) {
    $attributearraysingle = [];
    foreach ($configuration as $attributeId => $value) {
        $attributearraysingle[$variantNames[$attributeId]] = $value;
    }
    $attributearray[] = $attributearraysingle;
}

?>
<div class="card" style="border:solid 1px black;">
    <div class="card-header accordion-button" data-bs-toggle="collapse" data-bs-target="#customvarientval" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
        <div class="card-title" id="title_val">
            Variant Data
        </div>
    </div>
    <div class="card-body default-variant-section accordion-collapse collapse show" id="customvarientval">
        <div class="row">
            <div class="col-md-1 mt-4">
                <label>ALL</label>
                <input type="checkbox" name="checkall" class="checkall" value="checkall">
            </div>
            <div class="col-md-2 mt-4 text-left">
                <strong>Image</strong>
            </div>
            <div class="col-md-2 mt-4 text-left">
                <strong>Variant Name</strong>
            </div>
            <div class="col-md-3 mt-4 text-left">
                <strong>Price</strong>
                <input type="text" name="globalvarientprice" id="globalvarientprice" placeholder="0.00-5000" class="form-control" onkeypress="return isFloatNumber(this,event)">
            </div>
            <div class="col-md-4 mt-4 text-left">
                <strong>QTY</strong>
                <input type="text" name="globalvarientqty" id="globalvarientqty" placeholder="200" class="form-control" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" readonly>
            </div>
        </div>


    </div>
    @foreach($configurations as $varient=>$singlvarient)
    <?php

    $varientname = array();
    foreach ($singlvarient as $singlevalue) {
        $varientname[] = $singlevalue;
    }

    ?>

    <div class="card-body default-variant-section accordion-collapse collapse show" id="customvarientval">
        <?php
        $varientname = implode('-', $varientname);

        $qty = isset($vaientvaluedata[$varientname]) ? $vaientvaluedata[$varientname]['qty'] : 0;
        $price = isset($vaientvaluedata[$varientname]) ? $vaientvaluedata[$varientname]['selling_price'] : 0;

        $subproductid = isset($vaientvaluedata[$varientname]) ? $vaientvaluedata[$varientname]['subproduct_id'] : 0;


        ?>
        <div class="row" style="border:dotted 1px black; padding: 20px 0px;">
            <div class="col-md-1 mt-4">
                <input type="checkbox" name="varientid[{{$varientname}}]" class="singlecheckbox" value="{{$varientname}}">
                <input type="hidden" name="productvarientname[]" value="{{$varientname}}">
                <input type="hidden" name="useattribute[{{$varientname}}]" value="{{json_encode($attributearray[$varient])}}">

                <input type="hidden" name="newsubproduct[]" value="{{$subproductid}}">

            </div>
            <div class="col-md-2">
                <div class="_Icon_12h4v_1 _mediumIcon_12h4v_24" style="width: 70px;height: 70px;"><span class="Polaris-Icon Polaris-Icon--toneInteractive"><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                            <path d="M9.018 3.5h1.964c.813 0 1.469 0 2 .043.546.045 1.026.14 1.47.366.706.36 1.28.933 1.64 1.639.226.444.32.924.365 1.47.043.531.043 1.187.043 2v.982c0 .414-.336.75-.75.75s-.75-.336-.75-.75v-.95c0-.852 0-1.447-.038-1.91-.037-.453-.107-.714-.207-.911-.216-.424-.56-.768-.984-.984-.197-.1-.458-.17-.912-.207-.462-.037-1.056-.038-1.909-.038h-1.9c-.852 0-1.447 0-1.91.038-.453.037-.714.107-.911.207-.424.216-.768.56-.984.984-.1.197-.17.458-.207.912-.037.462-.038 1.057-.038 1.909v1.39l1.013-1.013c.683-.684 1.791-.684 2.474 0l2.543 2.543c.293.293.293.767 0 1.06-.293.293-.767.293-1.06 0l-2.543-2.543c-.098-.097-.256-.097-.354 0l-2.054 2.055c.005.113.011.218.02.317.036.454.106.715.206.912.216.424.56.768.984.984.197.1.458.17.912.207.462.037 1.057.038 1.909.038h.95c.414 0 .75.336.75.75s-.336.75-.75.75h-.982c-.813 0-1.469 0-2-.043-.546-.045-1.026-.14-1.47-.366-.706-.36-1.28-.933-1.64-1.639-.226-.444-.32-.924-.365-1.47-.015-.184-.024-.382-.03-.597-.015-.077-.017-.156-.006-.234-.007-.348-.007-.736-.007-1.169v-1.964c0-.813 0-1.469.043-2 .045-.546.14-1.026.366-1.47.36-.706.933-1.28 1.639-1.64.444-.226.924-.32 1.47-.365.531-.043 1.187-.043 2-.043Z"></path>
                            <path d="M12.5 9c.828 0 1.5-.672 1.5-1.5s-.672-1.5-1.5-1.5-1.5.672-1.5 1.5.672 1.5 1.5 1.5Z"></path>
                            <path d="M14.5 11.25c.414 0 .75.336.75.75v1.75h1.75c.414 0 .75.336.75.75s-.336.75-.75.75h-1.75v1.75c0 .414-.336.75-.75.75s-.75-.336-.75-.75v-1.75h-1.75c-.414 0-.75-.336-.75-.75s.336-.75.75-.75h1.75v-1.75c0-.414.336-.75.75-.75Z"></path>
                        </svg></span></div>
            </div>
            <div class="col-md-2 mt-3">
                <?php echo $varientname; ?>
            </div>
            <div class="col-md-3">
                <input type="text" name="varientprice[{{$varientname}}]" class="form-control varprice onlyprice" placeholder="0.00 - 500.00" onkeypress="return isFloatNumber(this,event)" value="{{$price}}">
            </div>
            <div class="col-md-4">
                <input type="text" name="varientqty[{{$varientname}}]" class="form-control varqty onlyqty" placeholder="100" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" value="{{$qty}}">
            </div>
        </div>


    </div>
    @endforeach
</div>