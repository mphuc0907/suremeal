
jQuery(function($) {
    $(".acf-field-63f8899da45fc").append("<div id='script-div'></div><div class='update-list-pl'><input type='button' value='Update' id='submit-save-pl' class='button button-primary button-large'></div>");
    $('.acf-field-645474d50f439').hide();
    // Load default
    var inputval1 = [];
    var inputkeyval1 = [];
    var inputval2 = [];
    var inputkeyval2 = [];
    loadPL1();
    loadPL2();
    loadDefaultHtml();
    // Hidden plus row pl1,pl2 and no drop
    $(".acf-field-63f8899da45fc .acf-icon.-plus").css("display","none");
    $(".acf-field-63fd81f2bd882 .acf-icon.-plus").css("display","none");
    $(".acf-field-63f8899da45fc .acf-row-handle.order").css("cursor","no-drop");
    $(".acf-field-63fd81f2bd882 .acf-row-handle.order").css("cursor","no-drop");
    // end Hidden plus row pl1,pl2 and no drop
    $(".grecaptcha-badge").addClass("hidden-im"); // Hidden captcha

    // Keyup plh1
    $("body").on('keyup change','.phan_loai_hang_1 input',function () {
        // Add text
        let val = $(this).val();
        var dataidthis = $(this).attr("id");
        $(".textpl1-"+dataidthis).text(val);
        // End add text pl1
        // Load array pl1, pl2
        loadPL1();
        loadPL2();
        // End load array pl1, pl2
        // Check show table pl1 or table pl2
        var checktitlepl2 = $("#title-pl2").text();
        if((inputkeyval2.length > 0 && !checktitlepl2) || (inputkeyval2.length == 0 && checktitlepl2)) { //
            loadDefaultHtml();
            showStart();
        }
        // End check show table ....
        if(inputkeyval1.length){
            for(var i = 0; i < inputkeyval1.length; i++) {
                var valone = inputval1[i];
                var dataid = inputkeyval1[i];
                var trpl1 = $(".tr-pl1-" + dataid).attr("data-key");
                if (valone != '' && !trpl1) {
                    // Kiểm tra xem đã tồn tại hàng này chưa
                    // Nếu chưa tồn tại hàng này
                    var html = "";
                    html = html + loadBody(i,dataid,valone);
                    $(".edit-admin-phanloai .ui-sortable").append(html);
                }
            }
        }

    });

    // Keyup plh2
    $("body").on('keyup change','.phan_loai_hang_2 input',function () {
        let val = $(this).val();
        var dataid = $(this).attr("id");
        $(".textpl2-"+dataid).text(val);

        // check show table 2
        var checktitlepl2 = $("#title-pl2").text();
        if((inputkeyval2.length > 0 && !checktitlepl2) || (inputkeyval2.length == 0 && checktitlepl2)){ // Load table pl2
            loadDefaultHtml();
            showStart();
        }

        // Get key array pl2
        let text2 = $(".phan_loai_hang_2 input");
        if(text2.length){
            for(var j = 0; j < text2.length; j++){
                var valpl2 = $(text2[j]).val();
                var dataid = $(text2[j]).attr("id");
                if(valpl2 != ''){
                    var trpl2 = $(".tr-pl2-"+dataid).attr("data-key");
                    if(!trpl2){ // Chưa tồn tại hàng này
                        var html = "";
                        var data2End = inputkeyval2[inputkeyval2.length-1];
                        var data2start = inputkeyval2[0];
                        inputval1.map(function (value,index) {
                            let key21 = inputkeyval1[index]+'-'+index;
                            html =  '<tr class="acf-row tr-pl1-'+inputkeyval1[index]+' tr-pl2-'+dataid+' "  data-key="'+dataid+'" >\n' +
                                '                            <td class="acf-field acf-field-text">\n' +
                                '                                <div class="acf-input">\n' +
                                '                                    <div class="acf-input-wrap textpl2-'+dataid+'">\n' +
                                '                                        '+ valpl2 +'\n' +
                                '                                    </div>\n' +
                                '                                </div>\n' +
                                '                            </td>\n' +
                                '                            <td class="acf-field acf-field-text">\n' +
                                '                                <div class="acf-input">\n' +
                                '                                    <div class="acf-input-wrap">\n' +
                                '                                        <input type="number" id="input1-'+key21+'" data-validate="validate-1-'+key21+'-error" data-id="'+key21+'" class="input-keyup input-keyup-price in1" placeholder="Nhập vào">\n' +
                                '                                    </div>\n' +
                                                            '<label id="validate-1-'+key21+'-error" class="errorr hidden" for="input1-'+key21+'">Không được để trống ô</label>' +
                                '                                </div>\n' +
                                '                            </td>\n' +
                                '                            <td class="acf-field acf-field-text">\n' +
                                '                                <div class="acf-input">\n' +
                                '                                    <div class="acf-input-wrap">\n' +
                                '                                        <input type="number" data-validate="validate-4-'+key21+'-error" data-id="'+key21+'" id="input4-'+key21+'"  class="input-keyup input-keyup-price in4" placeholder="Nhập vào">\n' +
                                '                                    </div>\n' +
                                                            '<label id="validate-4-'+key21+'-error" class="errorr hidden" for="input4-'+key21+'">Không được để trống ô</label>' +
                                '                                </div>\n' +
                                '                            </td>\n' +
                                '                            <td class="acf-field acf-field-text">\n' +
                                '                                <div class="acf-input">\n' +
                                '                                    <div class="acf-input-wrap">\n' +
                                '                                        <input type="number" id="input5-'+key21+'" class="input-keyup in5"  placeholder="Nhập vào">\n' +
                                '                                    </div>\n' +
                                                             '<label id="validate-5-'+key21+'-error" class="errorr hidden" for="input5-'+key21+'">Không được để trống ô</label>' +
                                '                                </div>\n' +
                                '                            </td>\n' +
                                '                            <td class="acf-field acf-field-text">\n' +
                                '                                <div class="acf-input">\n' +
                                '                                    <div class="acf-input-wrap">\n' +
                                '                                        <input type="number" data-validate="validate-2-'+key21+'-error" data-id="'+key21+'" id="input2-'+key21+'" class="input-keyup in2"  placeholder="Nhập vào">\n' +
                                '                                    </div>\n' +
                                                            '<label id="validate-2-'+key21+'-error" class="errorr hidden" for="input2-'+key21+'">Không được để trống ô</label>' +
                                '                                </div>\n' +
                                '                            </td>\n' +
                                '                            <td class="acf-field acf-field-text">\n' +
                                '                                <div class="acf-input">\n' +
                                '                                    <div class="acf-input-wrap">\n' +
                                '                                        <input type="text" id="input3-'+key21+'" class="input-keyup in3"  placeholder="Nhập vào">\n' +
                                '                                    </div>\n' +
                                '                                </div>\n' +
                                '                            </td>\n' +
                                '                        </tr>';
                            $(html).insertAfter($(".tr-pl2-" + data2End)[index]);
                        });
                        // if (dataid.indexOf('row') == -1) {
                        //
                        // }
                        // Set rowspan
                        $(".td2-"+data2start).attr("rowspan",text2.length-1);
                        loadPL2();
                    }
                }
            }
        }
        //
    });

    // PL1 function
    function loadPL1() {
        inputval1 = [];
        inputkeyval1 = [];
        let text_pl1 = $(".phan_loai_hang_1 input");
        if (text_pl1.length) {
            for (var j = 0; j < text_pl1.length; j++) {
                var valpl1 = $(text_pl1[j]).val();
                var dataid = $(text_pl1[j]).attr("id");
                if (valpl1 != '') {
                    inputval1.push(valpl1);
                    inputkeyval1.push(dataid);
                }
            }
        }
    }

    // PL2 function
    function loadPL2(){
        inputval2 = [];
        inputkeyval2 = [];
        let text_pl2 = $(".phan_loai_hang_2 input");
        if(text_pl2.length){
            for(var i = 0; i < text_pl2.length; i++){
                var valpl2 = $(text_pl2[i]).val();
                var dataid = $(text_pl2[i]).attr("id");
                if(valpl2 != ''){
                    inputval2.push(valpl2);
                    inputkeyval2.push(dataid);
                }
            }
        }

    }

    // Load default html
    var html_total = "";
    function loadDefaultHtml(){
        html_total = "";
        inputval1.map(function (value,index) {
            html_total = html_total + loadBody(index,inputkeyval1[index],value);
        });
        showStart();
    }

    var title_pl1 = $("#acf-field_63fd6b26da1c4").val()?$("#acf-field_63fd6b26da1c4").val():"Nhóm phân loại 1";
    var title_pl2 = $("#acf-field_63fd6c72da1c5").val()?$("#acf-field_63fd6c72da1c5").val():"Nhóm phân loại 2";
    $("#acf-field_63fd6b26da1c4").keyup(function () {
        var title_pl1 = $(this).val();
        $("#title-pl1").text(title_pl1);
    })
    $("#acf-field_63fd6c72da1c5").keyup(function () {
        var title_pl2 = $(this).val();
        $("#title-pl2").text(title_pl2);
    })


    function showStart() {
        var html2 = '';
        if (inputval2.length != 0) {
            html2 = '<th class="acf-th" style="width: 14%;"><label id="title-pl2">' + title_pl2 + '</label></th>';
        }
        var htmlShowStart = '<div class="acf-field acf-field-repeater edit-admin-phanloai">\n' +
            '    <div class="acf-label">\n' +
            '        <label for="acf-field_63f8899da45fc">Danh sách phân loại hàng</label>\n' +
            '    </div>\n' +
            '    <div class="acf-input">\n' +
            '        <div class="acf-repeater -table" >\n' +
            '            <table class="acf-table">\n' +
            '                <thead>\n' +
            '                    <tr>\n' +
            '                        <th class="acf-th" style="width: 10%;">\n' +
            '                            <label id="title-pl1">' + title_pl1 + '</label>\n' +
            '                        </th>\n' +
            html2 +
            '                        <th class="acf-th" style="width: 14%;">\n' +
            '                            <label><span class="acf-required">*</span> Giá</label>\n' +
            '                        </th>\n' +
            '                        <th class="acf-th" style="width: 14%;">\n' +
            '                            <label><span class="acf-required">*</span> Giá Bán</label>\n' +
            '                        </th>\n' +
            '                        <th class="acf-th" style="width: 14%;">\n' +
            '                            <label><span class="acf-required">*</span> Giá Flash sale</label>\n' +
            '                        </th>\n' +
            '                        <th class="acf-th" style="width: 14%;">\n' +
            '                            <label><span class="acf-required">*</span> Kho hàng </label>\n' +
            '                        </th>\n' +
            '                        <th class="acf-th" style="width: 14%;">\n' +
            '                            <label>SKU phân loại</label>\n' +
            '                        </th>\n' +
            '                    </tr>\n' +
            '                </thead>\n' +
            '                <tbody class="ui-sortable">\n' +
            html_total +
            '                </tbody>\n' +
            '            </table>\n' +
            '\n' +
            '        </div>\n' +
            '    </div>\n' +
            '</div>'
        $("#script-div").html(htmlShowStart);
    }

    // Save data
    $("body").on("click", "#submit-save-pl",function () {
        loadPL1();
        loadPL2();
        priceReal();
        savePLHang();
    });

    function savePLHang(){
        // loadPL1();
        // loadPL2();
        let pl1 = [];
        var check = 1;

        var checktitlepl2 = $("#title-pl2").text();
        inputkeyval1.map(function (value,index) {

            // Get value input pl1
            let checksave = 2;
            if(!checktitlepl2){ // Nếu không tồn tại pl2
                checksave = 1;
            }
            let classkey1 = ".tr-pl1-"+value+" .in1";
            let classkey2 = ".tr-pl1-"+value+" .in2";
            let classkey3 = ".tr-pl1-"+value+" .in3";
            let classkey4 = ".tr-pl1-"+value+" .in4";
            let classkey5 = ".tr-pl1-"+value+" .in5";
            let resultParent1 = $(classkey1).val();
            let resultParent2 = $(classkey2).val();
            let resultParent3 = $(classkey3).val();
            let resultParent4 = $(classkey4).val();
            let resultParent5 = $(classkey5).val();

            let id = ".tr-pl1-"+value;
            let array1 = {};
            array1.case1 = value;
            array1.value1 = resultParent1;
            array1.value2 = resultParent2;
            array1.value3 = resultParent3;
            array1.value4 = resultParent4;
            array1.value5 = resultParent5;
            array1.check = checksave;
            // Validate
            var dataid1 =  value+'-'+index;
            if(!resultParent1){
                $('#input1-' + dataid1).addClass('errorr');
                $('#validate-1-' + dataid1 + '-error').removeClass('hidden');
            }else{
                $('#input1-' + dataid1).removeClass('errorr');
                $('#validate-1-' + dataid1 + '-error').addClass('hidden');
            }
            if(!resultParent2){
                $('#input2-' + dataid1).addClass('errorr');
                $('#validate-2-' + dataid1 + '-error').removeClass('hidden');
            }else{
                $('#input2-' + dataid1).removeClass('errorr');
                $('#validate-2-' + dataid1 + '-error').addClass('hidden');
            }
            if(!resultParent4){
                $('#input4-' + dataid1).addClass('errorr');
                $('#validate-4-' + dataid1 + '-error').removeClass('hidden');
            }else{
                $('#input4-' + dataid1).removeClass('errorr');
                $('#validate-4-' + dataid1 + '-error').addClass('hidden');
            }
            if(!resultParent5){
                $('#input5-' + dataid1).addClass('errorr');
                $('#validate-5-' + dataid1 + '-error').removeClass('hidden');
            }else{
                $('#input5-' + dataid1).removeClass('errorr');
                $('#validate-5-' + dataid1 + '-error').addClass('hidden');
            }
            if(!resultParent1 || !resultParent2 || !resultParent4 || !resultParent5){
                check = 2;
            }
            // End get value input pl1
            let arrayt1 = [];
            let arrayt2 = [];
            let arrayt3 = [];
            let arrayt4 = [];
            let arrayt5 = [];
            inputkeyval2.map(function (value2,index2) {
                var id2 = ".tr-pl2-"+value2;
                var classtext1 = id+id2+" .in1";
                var classtext2 = id+id2+" .in2";
                var classtext3 = id+id2+" .in3";
                var classtext4 = id+id2+" .in4";
                var classtext5 = id+id2+" .in5";
                var result1 = $(classtext1).val();
                var result2 = $(classtext2).val();
                var result3 = $(classtext3).val();
                var result4 = $(classtext4).val();
                var result5 = $(classtext5).val();
                arrayt1.push(result1);
                arrayt2.push(result2);
                arrayt3.push(result3);
                arrayt4.push(result4);
                arrayt5.push(result5);

                // Validate pl2
                var dataid =  value+'-'+index2;
                if(!result1){
                    $('#input1-' + dataid).addClass('errorr');
                    $('#validate-1-' + dataid + '-error').removeClass('hidden');
                }else{
                    $('#input1-' + dataid).removeClass('errorr');
                    $('#validate-1-' + dataid + '-error').addClass('hidden');
                }
                if(!result2){
                    $('#input2-' + dataid).addClass('errorr');
                    $('#validate-2-' + dataid + '-error').removeClass('hidden');
                }else{
                    $('#input2-' + dataid).removeClass('errorr');
                    $('#validate-2-' + dataid + '-error').addClass('hidden');
                }
                if(!result4){
                    $('#input4-' + dataid).addClass('errorr');
                    $('#validate-4-' + dataid + '-error').removeClass('hidden');
                }else{
                    $('#input4-' + dataid).removeClass('errorr');
                    $('#validate-4-' + dataid + '-error').addClass('hidden');
                }
                if(!result5){
                    $('#input5-' + dataid).addClass('errorr');
                    $('#validate-5-' + dataid + '-error').removeClass('hidden');
                }else{
                    $('#input5-' + dataid).removeClass('errorr');
                    $('#validate-5-' + dataid + '-error').addClass('hidden');
                }
                if(!result1 || !result2 || !result4 || !result5){
                    check = 2;
                }
            });

            array1.chil1 = arrayt1;
            array1.chil2 = arrayt2;
            array1.chil3 = arrayt3;
            array1.chil4 = arrayt4;
            array1.chil5 = arrayt5;
            pl1.push(array1);
        });
        if(check == 1){
            let urlAjax = $("#urlAjax").val();
            let site_key = $("#site_key").val();
            let success_code = $("#success_code").val();
            let post_ID = $("#post_ID").val();

            grecaptcha.ready(function() {
                grecaptcha.execute(site_key, {action: 'subscribe_plhang'}).then(function(token) {
                    $.ajax({
                        url: urlAjax,
                        type: 'POST',
                        cache: false,
                        dataType: "json",
                        data: {
                            pl1,
                            post_ID,
                            action: 'Phanloaihang',
                            action1: "subscribe_plhang",
                            token1: token
                        },
                        beforeSend: function () {
                            $('.divgif').css('display', 'block');
                        },
                        success: function (rs) {
                            $('.divgif').css('display', 'none');
                            if (rs.status == success_code) {
                                $("#post").submit();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    text: rs.mess,
                                });
                            }
                        }
                    });
                    return false;
                });
            });
        }
    }
    $("body.post-type-san_pham").on("click", "#publish",function (event) {
        loadPL1();
        loadPL2();
        priceReal();
        //
        // if(inputkeyval1.length == 0){
        //     $("#post").submit();
        // }else{
            savePLHang();
        // }
        event.preventDefault();
    })

    var key1 = "acf-field_63f8899da45fc";
    var key2 = "field_63f8899da45fd";
    var keydelete = "";
    $("body").on("click", ".acf-js-tooltip", function () {
        var key = $(this).attr("data-event");
        if(key == "remove-row"){
            var parent  = $(this).closest(".acf-row");
            var id = $(parent).attr("data-id");
            keydelete = key1 + "-" + id + "-" + key2;

        }
    });
    // Remove
    function removeTr(){
        $("body").on("click", ".top a", function () {
            var confirm = $(this).attr("data-event");
            console.log(confirm);
            if(confirm == "confirm"){

            }
        })
    }

    // Load html phân loại
    function loadBody(key,valueinput1,valueinput2) {
        var html = "";
        if(inputval2.length > 0) { // TH nếu có pl 2
            inputval2.map(function (value2, index2) {
                let key12 = valueinput1+'-'+index2;
                if (value2 == inputval2[0]) {
                    html = html + '<tr  class="acf-row tr-pl1-' + valueinput1 + ' tr-pl2-' + inputkeyval2[index2] + '" data-key="' + inputkeyval2[index2] + '" >\n' +
                        '<td class="acf-field acf-field-text td1-' + valueinput1 + ' td2-' + inputkeyval2[index2] + '" rowspan="' + inputval2.length + '">\n' +
                        '                                <div class="acf-input">\n' +
                        '                                    <div class="acf-input-wrap textpl1-' + valueinput1 + '">\n' +
                        '                                        ' + valueinput2 + '\n' +
                        '                                    </div>\n' +
                        '                                </div>\n' +
                        '                            </td>' +
                        '                            <td class="acf-field acf-field-text">\n' +
                        '                                <div class="acf-input">\n' +
                        '                                    <div class="acf-input-wrap textpl2-' + inputkeyval2[index2] + '">\n' +
                        '                                        ' + value2 + '\n' +
                        '                                    </div>\n' +
                        '                                </div>\n' +
                        '                            </td>\n' +
                        '                            <td class="acf-field acf-field-text">\n' +
                        '                                <div class="acf-input">\n' +
                        '                                    <div class="acf-input-wrap">\n' +
                        '                                        <input type="number" id="input1-'+key12+'" data-validate="validate-1-'+key12+'-error" data-id="'+key12+'" class="input-keyup input-keyup-price in1"  placeholder="Nhập vào">\n' +
                        '                                    </div>\n' +
                                                        '<label id="validate-1-'+key12+'-error" class="errorr hidden" for="input1-'+key12+'">Không được để trống ô</label>' +
                        '                                </div>\n' +
                        '                            </td>\n' +
                        '                            <td class="acf-field acf-field-text">\n' +
                        '                                <div class="acf-input">\n' +
                        '                                    <div class="acf-input-wrap">\n' +
                        '                                        <input type="number" id="input4-'+key12+'" data-validate="validate-4-'+key12+'-error" data-id="'+key12+'" class="input-keyup input-keyup-price in4"  placeholder="Nhập vào">\n' +
                        '                                    </div>\n' +
                                                        '<label id="validate-4-'+key12+'-error" class="errorr hidden" for="input4-'+key12+'">Không được để trống ô</label>' +
                        '                                </div>\n' +
                        '                            </td>\n' +
                        '                            <td class="acf-field acf-field-text">\n' +
                        '                                <div class="acf-input">\n' +
                        '                                    <div class="acf-input-wrap">\n' +
                        '                                        <input type="number" id="input5-'+key12+'" class="input-keyup in5"  placeholder="Nhập vào">\n' +
                        '                                    </div>\n' +
                                                     '<label id="validate-5-'+key12+'-error" class="errorr hidden" for="input5-'+key12+'">Không được để trống ô</label>' +
                        '                                </div>\n' +
                        '                            </td>\n' +
                        '                            <td class="acf-field acf-field-text">\n' +
                        '                                <div class="acf-input">\n' +
                        '                                    <div class="acf-input-wrap">\n' +
                        '                                        <input type="number" id="input2-'+key12+'" data-validate="validate-2-'+key12+'-error" data-id="'+key12+'" class="input-keyup in2" placeholder="Nhập vào">\n' +
                        '                                    </div>\n' +
                                                        '<label id="validate-2-'+key12+'-error" class="errorr hidden" for="input2-'+key12+'">Không được để trống ô</label>' +
                        '                                </div>\n' +
                        '                            </td>\n' +
                        '                            <td class="acf-field acf-field-text">\n' +
                        '                                <div class="acf-input">\n' +
                        '                                    <div class="acf-input-wrap">\n' +
                        '                                        <input type="text" id="input3-'+key12+'" class="input-keyup in3"  placeholder="Nhập vào">\n' +
                        '                                    </div>\n' +
                        '                                </div>\n' +
                        '                            </td>\n' +
                        '                        </tr>';
                } else {
                    html = html + '<tr class="acf-row tr-pl1-' + valueinput1 + ' tr-pl2-' + inputkeyval2[index2] + ' "  data-key="' + inputkeyval2[index2] + '" >\n' +
                        '                            <td class="acf-field acf-field-text">\n' +
                        '                                <div class="acf-input">\n' +
                        '                                    <div class="acf-input-wrap textpl2-' + inputkeyval2[index2] + '">\n' +
                        '                                        ' + value2 + '\n' +
                        '                                    </div>\n' +
                        '                                </div>\n' +
                        '                            </td>\n' +
                        '                            <td class="acf-field acf-field-text">\n' +
                        '                                <div class="acf-input">\n' +
                        '                                    <div class="acf-input-wrap">\n' +
                        '                                        <input type="number" id="input1-'+key12+'" data-validate="validate-1-'+key12+'-error" data-id="'+key12+'" class="input-keyup input-keyup-price in1"  placeholder="Nhập vào">\n' +
                        '                                    </div>\n' +
                                                        '<label id="validate-1-'+key12+'-error" class="errorr hidden" for="input1-'+key12+'">Không được để trống ô</label>' +
                        '                                </div>\n' +
                        '                            </td>\n' +
                        '                            <td class="acf-field acf-field-text">\n' +
                        '                                <div class="acf-input">\n' +
                        '                                    <div class="acf-input-wrap">\n' +
                        '                                        <input type="number" id="input4-'+key12+'" data-validate="validate-4-'+key12+'-error" data-id="'+key12+'" class="input-keyup input-keyup-price in4"  placeholder="Nhập vào">\n' +
                        '                                    </div>\n' +
                                                    '<label id="validate-4-'+key12+'-error" class="errorr hidden" for="input4-'+key12+'">Không được để trống ô</label>' +
                        '                                </div>\n' +
                        '                            </td>\n' +
                        '                            <td class="acf-field acf-field-text">\n' +
                        '                                <div class="acf-input">\n' +
                        '                                    <div class="acf-input-wrap">\n' +
                        '                                        <input type="number" id="input5-'+key12+'" class="input-keyup in5"  placeholder="Nhập vào">\n' +
                        '                                    </div>\n' +
                                                    '<label id="validate-5-'+key12+'-error" class="errorr hidden" for="input5-'+key12+'">Không được để trống ô</label>' +
                        '                                </div>\n' +
                        '                            </td>\n' +
                        '                            <td class="acf-field acf-field-text">\n' +
                        '                                <div class="acf-input">\n' +
                        '                                    <div class="acf-input-wrap">\n' +
                        '                                         <input type="number" id="input2-'+key12+'" data-validate="validate-2-'+key12+'-error" data-id="'+key12+'" class="input-keyup in2" placeholder="Nhập vào">\n' +
                        '                                    </div>\n' +
                                                    '<label id="validate-2-'+key12+'-error" class="errorr hidden" for="input2-'+key12+'">Không được để trống ô</label>' +
                        '                                </div>\n' +
                        '                            </td>\n' +
                        '                            <td class="acf-field acf-field-text">\n' +
                        '                                <div class="acf-input">\n' +
                        '                                    <div class="acf-input-wrap">\n' +
                        '                                        <input type="text" id="input3-'+key12+'" class="input-keyup in3"  placeholder="Nhập vào">\n' +
                        '                                    </div>\n' +
                        '                                </div>\n' +
                        '                            </td>\n' +
                        '                        </tr>';
                }
            });

        }else{ // TH nếu chỉ có pl1
            let key1 = valueinput1+'-'+key;
            html = html + '<tr class="acf-row tr-pl1-'+valueinput1+'"  data-key="'+valueinput1+'" >\n' +
                '                            <td class="acf-field acf-field-text">\n' +
                '                                <div class="acf-input">\n' +
                '                                    <div class="acf-input-wrap textpl1-'+valueinput1+'">\n' +
                '                                        '+valueinput2+'\n' +
                '                                    </div>\n' +
                '                                </div>\n' +
                '                            </td>\n' +
                '                            <td class="acf-field acf-field-text">\n' +
                '                                <div class="acf-input">\n' +
                '                                    <div class="acf-input-wrap">\n' +
                '                                        <input type="number" id="input1-'+key1+'" data-validate="validate-1-'+key1+'-error" data-id="'+key1+'" class="input-keyup input-keyup-price in1"  placeholder="Nhập vào">\n' +
                '                                    </div>\n' +
                '<label id="validate-1-'+key1+'-error" class="errorr hidden" for="input1-'+key1+'">Không được để trống ô</label>' +
                '                                </div>\n' +
                '                            </td>\n' +
                '                            <td class="acf-field acf-field-text">\n' +
                '                                <div class="acf-input">\n' +
                '                                    <div class="acf-input-wrap">\n' +
                '                                        <input type="number" id="input4-'+key1+'" data-validate="validate-4-'+key1+'-error" data-id="'+key1+'" class="input-keyup input-keyup-price in4"  placeholder="Nhập vào">\n' +
                '                                    </div>\n' +
                '<label id="validate-4-'+key1+'-error" class="errorr hidden" for="input4-'+key1+'">Không được để trống ô</label>' +
                '                                </div>\n' +
                '                            </td>\n' +
                '                            <td class="acf-field acf-field-text">\n' +
                '                                <div class="acf-input">\n' +
                '                                    <div class="acf-input-wrap">\n' +
                '                                        <input type="number" id="input5-'+key1+'" class="input-keyup in5"  placeholder="Nhập vào">\n' +
                '                                    </div>\n' +
                '<label id="validate-5-'+key1+'-error" class="errorr hidden" for="input5-'+key1+'">Không được để trống ô</label>' +
                '                                </div>\n' +
                '                            </td>\n' +
                '                            <td class="acf-field acf-field-text">\n' +
                '                                <div class="acf-input">\n' +
                '                                    <div class="acf-input-wrap">\n' +
                '                                         <input type="number" id="input2-'+key1+'" data-validate="validate-2-'+key1+'-error" data-id="'+key1+'" class="input-keyup in2" placeholder="Nhập vào">\n' +
                '                                    </div>\n' +
                '<label id="validate-2-'+key1+'-error" class="errorr hidden" for="input2-'+key1+'">Không được để trống ô</label>' +
                '                                </div>\n' +
                '                            </td>\n' +
                '                            <td class="acf-field acf-field-text">\n' +
                '                                <div class="acf-input">\n' +
                '                                    <div class="acf-input-wrap">\n' +
                '                                        <input type="text" id="input3-'+key1+'" class="input-keyup in3"  placeholder="Nhập vào">\n' +
                '                                    </div>\n' +
                '                                </div>\n' +
                '                            </td>\n' +
                '                        </tr>';
        }
        return html;
    }

    // $('input[type="number"]').keyup(function(){
    //     var data = $(this).val();
    //     var regx = /^[0-9]+$/;
    //
    //     // console.log( data + ' patt:'+ data.match(regx));
    //
    //     if ( data === '' || data.match(regx) ){
    //         $('.amt_err').fadeOut('slow');
    //     }
    //     else {
    //         $(this).val("");
    //         $('.amt_err')
    //             .text('only Numeric Digits(0 to 9) allowed!')
    //             .css({'color':'#fff', 'background':'#990000', 'padding':'3px'})
    //             .fadeIn('fast');
    //     }
    // });

    // Keyup validate
    $("body").on("keyup",".input-keyup-price",function () {
        var val = $(this).val();
        var datavalidate = $(this).attr("data-validate");
        if(!val){
            $(this).addClass('errorr');
            $('#'+datavalidate).removeClass('hidden');
        }else{
            $(this).removeClass('errorr');
            $('#'+datavalidate).addClass('hidden');
        }
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });

    // Load default
    if($("#post_ID").val()) {
        loadDefault();
    }
    function loadDefault() {
        let urlAjax = $("#urlAjax").val();
        let site_key = $("#site_key").val();
        let success_code = $("#success_code").val();
        let post_ID = $("#post_ID").val();
        let status = $("#original_post_status").val();
        grecaptcha.ready(function() {
            grecaptcha.execute(site_key, {action: 'subscribe_loadl_plhang'}).then(function(token) {
                $.ajax({
                    url: urlAjax,
                    type: 'POST',
                    cache: false,
                    dataType: "json",
                    data: {
                        post_ID,
                        status,
                        action: 'LoadPhanloaihang',
                        action1: "subscribe_loadl_plhang",
                        token1: token
                    },
                    beforeSend: function () {
                        $('.divgif').css('display', 'block');
                    },
                    success: function (rs) {
                        $('.divgif').css('display', 'none');
                        if (rs.status == success_code) {
                            $("#title-pl1").text(rs.title_phanloai);
                            $("#title-pl2").text(rs.title_phanloai_2);
                            let respon = rs.data;
                            inputkeyval1.map(function (value,index) {
                                if(respon[index].check == "1"){
                                    var dataid1 = value+"-"+index;
                                    $("#input1-"+dataid1).val(respon[index].value1);
                                    $("#input2-"+dataid1).val(respon[index].value2);
                                    $("#input3-"+dataid1).val(respon[index].value3);
                                    $("#input4-"+dataid1).val(respon[index].value4);
                                    $("#input5-"+dataid1).val(respon[index].value5);
                                }else{
                                    inputkeyval2.map(function (value2,index2) {
                                        var dataid = value+"-"+index2;
                                        $("#input1-"+dataid).val(respon[index].chil1[index2]);
                                        $("#input2-"+dataid).val(respon[index].chil2[index2]);
                                        $("#input3-"+dataid).val(respon[index].chil3[index2]);
                                        $("#input4-"+dataid).val(respon[index].chil4[index2]);
                                        $("#input5-"+dataid).val(respon[index].chil5[index2]);
                                    });
                                }

                            });
                        } else {
                            // Swal.fire({
                            //     icon: 'error',
                            //     text: rs.mess,
                            // });
                        }
                    }
                });
                return false;
            });
        });
    }

    $('#acf-field_645474d50f439').attr('readonly', true);

    function priceReal() {
        var priceReal = $('#input4-' + inputkeyval1[0] +'-'+0).val();
        var checks = 0;
        if(inputkeyval1.length == 0) {
            var priceProduct = $('#acf-field_63f83728c6cf0').val();
            var pricePromotion = $('#acf-field_63f884cd772f3').val();
            if(pricePromotion > 0) {
                // $('#acf-field_645474d50f439').val(pricePromotion);
                priceReal = pricePromotion;
            } else {
                // $('#acf-field_645474d50f439').val(priceProduct);
                priceReal = priceProduct;
            }
        } else if(inputkeyval2.length == 0) {
            inputkeyval1.map(function (value, key) {
                var pric = $('#input4-' + value +'-'+key).val();
                if(parseFloat(pric) < parseFloat(priceReal)) {
                    priceReal = pric;
                }
            });

        } else {
            inputkeyval1.map(function (value, key) {
                var id = ".tr-pl1-"+value;
                inputkeyval2.map(function (value2, key2) {
                    var id2 = ".tr-pl2-"+value2;
                    var pric = $(id+id2+" .in4").val();
                    if(checks == 0) {
                        priceReal = $(id+id2+" .in4").val();
                        checks = 1;
                    }
                    if(parseFloat(pric) < parseFloat(priceReal)) {
                        priceReal = pric;
                    }
                });
            });
        }
        $('#acf-field_645474d50f439').val(priceReal);
    }
});