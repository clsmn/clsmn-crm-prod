$(function() {
    var cloudCallInterval = '';
    $('#showLeadModal').on('show.bs.modal', function (e) {
        if(e.namespace == 'bs.modal')
        {
            var leadId = $('#showLeadModal').attr('data-val');
            $("#modalBody").load(baseURL+"/admin/lead/"+leadId, function(){
                
                $('#txtUserAddress').addGeolocation();
                loadInlineConfirmation();
                alternateNumberLoadInlineConfirmation();
                $('.masking').inputmask();
                $('.datemask').inputmask("dd/mm/yyyy");
                getLearningChildren(leadId);
                getLeadDetail(leadId);
                $('#scheduleDemoDatePicker').datetimepicker({
                    format: "dd MM yyyy - HH:ii P",
                    showMeridian: true,
                    pickerPosition: "top-left",
                    linkField: "scheduleDemoTime",
                    linkFormat: "yyyy-mm-dd hh:ii"
                });
                $('#nextFollowUpTimePicker').datetimepicker({
                    format: "dd MM yyyy - HH:ii P",
                    showMeridian: true,
                    pickerPosition: "top-left",
                    linkField: "nextFollowUpTime",
                    linkFormat: "yyyy-mm-dd hh:ii"
                });
            });
        }
    });

    function getLeadDetail(leadId)
    {
        $.ajax({
            url     : baseURL+'/admin/ajax/getLeadDetail/'+leadId,
            type    : 'get',
            success : function(response)
            {
                if(response.Status == '200')
                {
                    var html = '<button class="btn '+response.Data.status_class+'">'+response.Data.lead_status+'</button> ('+response.Data.data_medium+')'+'<span class="assign text-center" style="padding-left:25%"> '+response.Data.country_code+'-'+response.Data.phone+'</span><span class="assign float-right">Assigned To: '+response.Data.assigned_to_name+'</span>'
                    $("#showLeadModal").find('.modal-title').find('span').html(html);
                }
            }
        });
    }

    function loadInlineConfirmation()
    {
        $('[data-toggle=confirmation]').confirmation({
            rootSelector: '[data-toggle=confirmation]',
            container: 'body',
            onConfirm: function() {
                var ths = $(this);
                var childId = ths.attr('data-value');
                $.ajax({
                    url: baseURL+'/admin/ajax/removeChild/'+childId,
                    type:'delete',
                    success:function(response)
                    {
                        if(response.Status == '200')
                        {
                            ths.parents('tr').remove();
                        }
                    }
                })
            },
        });
    }

    function alternateNumberLoadInlineConfirmation()
    {
        $('[data-toggle=an-confirmation]').confirmation({
            rootSelector: '[data-toggle=an-confirmation]',
            container: 'body',
            onConfirm: function() {
                var ths = $(this);
                var numberId = ths.attr('data-value');
                $.ajax({
                    url: baseURL+'/admin/ajax/deleteAlternateNumber/'+numberId,
                    type:'delete',
                    success:function(response)
                    {
                        if(response.Status == '200')
                        {
                            ths.parents('tr').remove();
                        }
                    }
                })
            },
        });
    }

    $('body').on('click', '#toggleAlternateNumber', function(){
        if($('.alternate-number').hasClass('hide'))
        {
            $('.alternate-number').removeClass('hide');
        }else{
            $('.alternate-number').addClass('hide');
        }
    });

    $('body').on('click', '.primary-number > span', function(){
        var ths = $(this);
        var type = ths.attr('data-type');
        var leadId = ths.attr('data-lead');
        var id = ths.attr('data-val');
        $.ajax({
            url     : baseURL+'/admin/ajax/setPrimaryNumber',
            type    : 'get',
            data    : 'type='+type+'&leadId='+leadId+'&id='+id,
            success : function(response)
            {
                if(response.Status == '200')
                {
                    refreshModal(leadId);
                }else if(response.Status == '201' )
                {
                    var html = '<div class="alert alert-danger alert-dismissible">';
                        html += '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
                        html += response.Message;
                        html += '</div>';
                    $('#alternateNumberMsg').html(html);
                }
            }
        });
    });

    $('body').on('click', '.saveAlternateNumber', function(){
        var ths = $(this);
        var leadId = ths.attr('data-lead');
        var preferred = ths.attr('data-val');
        var phone = $('#alertnatePhone').val();
        var name = $('#alertnatePhoneName').val();
        var relation = $('#alertnatePhoneRelation').val();
        var err = false;
        $('#alertnatePhone').next('div.error').html('');
        $('#alertnatePhoneName').next('div.error').html('');

        if(phone == '')
        {
            err = true;
            $('#alertnatePhone').next('div.error').html('This is required');
        }
        if(phone.length != '11')
        {
            err = true;
            $('#alertnatePhone').next('div.error').html('Enter valid number');
        }
        if(name == '')
        {
            err = true;
            $('#alertnatePhoneName').next('div.error').html('This is required');
        }
        if(!err)
        {
            $.ajax({
                url     : baseURL+'/admin/ajax/addAlternateNumber/'+leadId,
                type    : 'post',
                data    : 'preferred='+preferred+'&phone='+phone+'&name='+name+'&relation='+relation,
                success : function(response)
                {
                    if(response.Status == '201' || response.Status == '202' )
                    {
                        $('#alertnatePhone').val('');
                        $('#alertnatePhoneName').val('');
                        $('#alertnatePhoneRelation').val('');
    
                        var row = response.Data;
                        var html = '<tr>';
                        if(row.preferred == '1')
                        {
                            var cls = 'text-green';
                            $('.primary-number').removeClass('text-green');
                        }else{
                            var cls = '';
                        }
                        html += '<td class="'+cls+' primary-number">';
                        html += '<span data-type="alternate" data-val="'+row.id+'" data-lead="'+row.lead_id+'">';
                        html += '<i class="fa fa-check-circle-o"></i>';
                        html += '</span>';
                        html += '</td>';
                        html += '<td>'+row.phone+'</td>';
                        if(row.name != 'null')
                        {
                            html += '<td>'+row.name+'</td>';
                        }else{
                            html += '<td></td>';
                        }
                        if(row.relation != 'null')
                        {
                            html += '<td>'+row.relation+'</td>';
                        }else{
                            html += '<td></td>';
                        }
                        html += '</tr>';
                        $('#leadDetail >tbody').append(html);
    
                        if(response.Status == '201')
                        {
                            var html = '<div class="alert alert-danger alert-dismissible">';
                                html += '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
                                html += response.Message;
                                html += '</div>';
                            $('#alternateNumberMsg').html(html);
                        }
                    }else if(response.Status == '200')
                    {
                        //refresh modal popup
                        refreshModal(leadId);
                    }
                }
            });
        }
    });

    $('body').on('click', '.refreshLead', function(){
        let leadId = $(this).attr('data-val');
        refreshModal(leadId);
    });

    function refreshModal(leadId)
    {
        $("#modalBody").load(baseURL+"/admin/lead/"+leadId, function(){
            $('#txtUserAddress').addGeolocation();
            loadInlineConfirmation();
            alternateNumberLoadInlineConfirmation();
            $('.masking').inputmask();
            $('.datemask').inputmask("dd/mm/yyyy");
            getLearningChildren(leadId);
            getLeadDetail(leadId);
            $('#scheduleDemoDatePicker').datetimepicker({
                format: "dd MM yyyy - HH:ii P",
                showMeridian: true,
                pickerPosition: "top-left",
                linkField: "scheduleDemoTime",
                linkFormat: "yyyy-mm-dd hh:ii"
            });
            $('#nextFollowUpTimePicker').datetimepicker({
                format: "dd MM yyyy - HH:ii P",
                showMeridian: true,
                pickerPosition: "top-left",
                linkField: "nextFollowUpTime",
                linkFormat: "yyyy-mm-dd hh:ii"
            });
        });
    } 

    //Add address
    $.fn.addGeolocation = function () { 
        this.geocomplete().bind("geocode:result", function(event, result){
            $("#txtUserCountry").val('');
            $("#txtUserState").val('');
            $("#txtUserCity").val('');
            $("#txtUserLocality").val('');
            $("#txtUserLatLng").val('');
            $("#txtUserAddress").val(result.formatted_address);
            var adrCom = JSON.stringify(result.address_components);
            var addressComponent = result.address_components;
            var userLocality = "";
            var userCity = "";
            var userState = "";
            var userCountry = "";
            for (let index = 0; index < addressComponent.length; index++) 
            {
                if(addressComponent[index].types[0]=="country")
                {
                    userCountry = addressComponent[index].long_name;
                }
                if(addressComponent[index].types[0]=="administrative_area_level_1")
                {
                    userState = addressComponent[index].long_name;
                }
                if(addressComponent[index].types[0]=="locality")
                {
                    userCity = addressComponent[index].long_name;
                }
                if(addressComponent[index].types[0]=="sublocality_level_1")
                {
                    userLocality = addressComponent[index].long_name;
                }
            }
            if(userCity!="" && userState!="" && userCountry!="")
            {
                $("#txtUserCountry").val(userCountry);
                $("#txtUserState").val(userState);
                $("#txtUserCity").val(userCity);
                $("#txtUserLocality").val(userLocality);
                $("#txtUserLatLng").val(result.geometry.location.lat()+","+result.geometry.location.lng());
            }else
            {
                alert("Please select a valid address.");
            }
        })
        .bind("geocode:error", function(event, status){
            $("#txtUserAddress").val("ERROR: " + status);
        })
        .bind("geocode:multiple", function(event, results){
            $("#txtUserAddress").val("Multiple: " + results.length + " results found");
        });
    }

    $('body').on('click', '#addAddress', function(){
        $('.add-address').removeClass('hide');
        $('#addAddress').addClass('hide');
    });

    $('body').on('click', '#cancelAddAddress', function(){
        $('.add-address').addClass('hide');
        $('#addAddress').removeClass('hide');
    });

    $('body').on('click', '#updateAddress', function(){
        var country  = $("#txtUserCountry").val();
        var state    = $("#txtUserState").val();
        var city     = $("#txtUserCity").val();
        var locality = $("#txtUserLocality").val();
        var latLong  = $("#txtUserLatLng").val();
        var address  = $("#txtUserAddress").val();
        var leadId   = $(this).attr('data-val');

        $.ajax({
            url : baseURL+'/admin/ajax/updateLeadAddress/'+leadId,
            type : 'post',
            data : 'country='+country+'&state='+state+'&city='+city+'&locality='+locality+'&lat_long='+latLong,
            success: function(response)
            {
                if(response.Status == '200')
                {
                    $('.userAddress').html(address);
                    $('.add-address').addClass('hide');
                    $('#addAddress').addClass('hide');            
                }
            }
        });
    });

    //Edit Child
    $('body').on('click', '.editChid', function(){
        var childId = $(this).attr('data-val');
        $('.child-value-'+childId).addClass('hide');
        $('.edit-child-'+childId).removeClass('hide');
    });

    $('body').on('click', '.cancelEditChild', function(){
        var childId = $(this).attr('data-val');
        $('.child-value-'+childId).removeClass('hide');
        $('.edit-child-'+childId).addClass('hide');
    });
    
    $('body').on('click', '.saveChild', function(){
        var childId = $(this).attr('data-val');
        var childName = $('#child_name-'+childId).find('.edit-child-'+childId).val();
        var childGender = $('#child_gender-'+childId).find('.edit-child-'+childId).val();
        var childDob = $('#child_dob-'+childId).find('.edit-child-'+childId).val();
        var childClass = $('#child_class-'+childId).find('.edit-child-'+childId).val();
        var childSchool = $('#child_school-'+childId).find('.edit-child-'+childId).val();

        $.ajax({
            url     : baseURL+'/admin/ajax/updateDataChild/'+childId,
            type    : 'post',
            data    : 'childName='+childName+'&childGender='+childGender+'&childDob='+childDob+'&childClass='+childClass+'&childSchool='+childSchool,
            success : function(response)
            {
                if(response.Status == '200')
                {
                    $('#child_name-'+childId).find('.child-value-'+childId).text(response.Data.childName);
                    $('#child_gender-'+childId).find('.child-value-'+childId).text(response.Data.childGender);
                    $('#child_dob-'+childId).find('.child-value-'+childId).text(response.Data.childAge);
                    $('#child_class-'+childId).find('.child-value-'+childId).text(response.Data.childClass);
                    $('#child_school-'+childId).find('.child-value-'+childId).text(response.Data.childSchool);

                    $('.child-value-'+childId).removeClass('hide');
                    $('.edit-child-'+childId).addClass('hide');
                }
            }
        });
    });

    //Add child
    $('body').on('click', '.addChildBtn', function(){
        $('#addChildTr').removeClass('hide');
    });

    $('body').on('click', '.cancelAddChild', function(){
        $('#child_name').val('');
        $('#child_gender').val('');
        $('#child_dob').val('');
        $('#child_class').val('');
        $('#child_school').val('');
        $('#addChildTr').addClass('hide');
    });

    $('body').on('click', '.addChild', function(){
        var leadId      = $(this).attr('data-val');
        var childName   = $('#child_name').val();
        var childGender = $('#child_gender').val();
        var childDob    = $('#child_dob').val();
        var childClass  = $('#child_class').val();
        var childSchool = $('#child_school').val();

        $.ajax({
            url     : baseURL+'/admin/ajax/addDataChild/'+leadId,
            type    : 'post',
            data    : 'childName='+childName+'&childGender='+childGender+'&childDob='+childDob+'&childClass='+childClass+'&childSchool='+childSchool,
            success : function(response)
            {
                if(response.Status == '200')
                {
                    var html = '<tr>';
                    html += '<td id="child_name-'+response.Data.childId+'">';
                    html += '<span class="child-value-'+response.Data.childId+'">'+response.Data.childName+'</span>';
                    html += '<input type="text" value="'+response.Data.childName+'" class="form-control input-sm hide edit-child-'+response.Data.childId+'">';
                    html += '</td>';
                    html += '<td id="child_gender-'+response.Data.childId+'">';
                    html += '<span class="child-value-'+response.Data.childId+'">'+response.Data.childGender+'</span>';
                    html += '<select class="form-control input-sm hide edit-child-'+response.Data.childId+'">';
                    html += '<option value="">Select Gender</option>';
                    html += '<option value="BOY" '+((response.Data.childGender == 'BOY')? 'selected="selected"' : '' )+'>Boy</option>';
                    html += '<option value="GIRL" '+((response.Data.childGender == 'GIRL')? 'selected="selected"' : '' )+'>Girl</option>';
                    html += '</select>';
                    html += '</td>';
                    html += '<td id="child_dob-'+response.Data.childId+'">';
                    html += '<span class="child-value-'+response.Data.childId+'">'+response.Data.childAge+'</span>';
                    html += '<input type="text" value="'+response.Data.childDob+'" class="form-control input-sm datemask hide edit-child-'+response.Data.childId+'" data-inputmask="\'alias\': \'dd/mm/yyyy\'" data-mask>';
                    html += '</td>';
                    html += '<td id="child_class-'+response.Data.childId+'">';
                    html += '<span class="child-value-'+response.Data.childId+'">'+response.Data.childClass+'</span>';
                    html += '<select class="form-control input-sm hide edit-child-'+response.Data.childId+'">';
                    html += '<option value="1" '+((response.Data.childGrade == '1')? 'selected="selected"':'')+'>Play Group</option>';
                    html += '<option value="2" '+((response.Data.childGrade == '2')? 'selected="selected"':'')+'>Nursery</option>';
                    html += '<option value="3" '+((response.Data.childGrade == '3')? 'selected="selected"':'')+'>Lower/Junior KG</option>';
                    html += '<option value="4" '+((response.Data.childGrade == '4')? 'selected="selected"':'')+'>Upper/Senior KG</option>';
                    html += '<option value="18" '+((response.Data.childGrade == '18')? 'selected="selected"':'')+'>Home Taught</option>';
                    html += '</select>';
                    html += '</td>';
                    html += '<td>'+response.Data.childMedium+'</td>';
                    html += '<td id="child_school-'+response.Data.childId+'">';
                    html += '<span class="child-value-'+response.Data.childId+'">'+response.Data.childSchool+'</span>';
                    html += '<input type="text" value="'+response.Data.childSchool+'" class="form-control input-sm hide edit-child-'+response.Data.childId+'">';
                    html += '</td>';
                    html += '<td>'+response.Data.childAdded+'</td>';
                    html += '<td>';
                    html += '<span class="child-value-'+response.Data.childId+'">';
                    html += '<button class="btn btn-xs btn-primary editChid" data-val="'+response.Data.childId+'" style="margin-right:4px;"><i class="fa fa-pencil"></i></button>';
                    html += '<button class="btn btn-xs btn-danger" data-toggle="confirmation" data-value="'+response.Data.childId+'" data-singleton="true">';
                    html += '<i class="fa fa-trash"></i>';
                    html += '</button>';
                    html += '</span>';
                    html += '<span class="hide edit-child-'+response.Data.childId+'">';
                    html += '<button class="btn btn-xs btn-success saveChild" data-val="'+response.Data.childId+'" style="margin-right:4px;"><i class="fa fa-check"></i></button>';
                    html += '<button class="btn btn-xs btn-danger cancelEditChild" data-val="'+response.Data.childId+'"><i class="fa fa-times"></i></button>';
                    html += '</span>';
                    html += '</td>';
                    html += '</tr>';

                    $(html).insertBefore('#addChildTr');
                    loadInlineConfirmation();

                    $('#child_name').val('');
                    $('#child_gender').val('');
                    $('#child_dob').val('');
                    $('#child_class').val('');
                    $('#child_school').val('');
                }
            }
        });
    });

    //Learning activation
    $('body').on('click', '#activateLearning', function(){
        var leadId = $(this).attr('data-val');
        $.ajax({
            url     : baseURL+'/admin/ajax/activateLearning/'+leadId,
            type    : 'POST',
            success : function(response)
            {
                if(response.Status == '200')
                {
                    getLearningChildren(leadId);
                    $('#activateLearning').parent('div.text-center').addClass('hide');
                    $('#startFreeTrialToggle').removeClass('hide');
                }
            }
        });
    });

    $('body').on('click', '#startFreeTrialToggle', function(){
        $('#startTrialTable').removeClass('hide');
    });

    $('body').on('click', '.cancelStartTrial', function(){
        $('#selectPackage').next('div.error').html('');
        $('#learningChildName').next('div.error').html('');
        $('#startTrialTable').addClass('hide');
    });
    
    function getLearningChildren(leadId)
    {
        $.ajax({
            url     : baseURL+'/admin/ajax/getLearningChildren/'+leadId,
            type    : 'get',
            success : function(response)
            {
                if(response.Status == '200')
                {
                    populateChildSelectBox(response.Data);
                }
            }
        });
    }
    
    function populateChildSelectBox(children)
    {
        if(children.length > 0)
        {
            var html = '<option value="">Select Child</option>';
            $.each(children, function(key, child){
                html += '<option value="'+child.id+'" data-dob="'+child.dob+'" data-class="'+child.child_class_id+'" data-name="'+child.name+'">'+child.name+'</option>';
            });
            $('#selectChild').html(html);
            $('#selectChild').parents('td').removeClass('hide');
        }else{
            $('#selectChild').parents('td').addClass('hide');
            $('#selectChild').val('');
        }
    }
    
    $('body').on('change', '#selectChild', function(){
        var ths = $(this);
        if(ths.val() != '')
        {
            var opt = $('#selectChild option:selected');
            $('#learningChildName').val(opt.attr('data-name'));
            $('#learningChildClass').val(opt.attr('data-class'));
            var childDOB = opt.attr('data-dob');
            if(typeof childDOB != null )
            {
                var d = new Date(childDOB);
                var mon = d.getMonth();
                mon++;
                childDOB = d.getDate()+'/'+mon+'/'+d.getFullYear();
                $('#learningChildDOB').val(childDOB);
            }
        }
    });

    $('body').on('click', '#startTrialSubmit', function(){
        var leadId = $(this).attr('data-val');
        var childId = $('#selectChild').val();
        var childName = $('#learningChildName').val();
        var childDob = $('#learningChildDOB').val();
        var childClass = $('#learningChildClass').val();
        var packageId = $('#selectPackage').val();
        var err = false;
        $('#selectPackage').next('div.error').html('');
        $('#learningChildName').next('div.error').html('');
        if(childName == '')
        {
            err = true;
            $('#learningChildName').next('div.error').html('Required field');
        }
        if(packageId == '')
        {
            err = true;
            $('#selectPackage').next('div.error').html('Required field');
        }
        if(!err)
        {
            if(typeof childId == 'undefined')
            {
                childId = 0;
            }
            $.ajax({
                url     : baseURL+'/admin/ajax/startLearningTrial/'+leadId,
                type    : 'post',
                data    : 'childId='+childId+'&childName='+childName+'&childDob='+childDob+'&childClass='+childClass+'&packageId='+packageId,
                success : function(response)
                {
                    if(response.Status == '200')
                    {
                        var html = '<tr>';
                        html += '<td>'+response.Data.package_name+'</td>';
                        html += '<td>'+response.Data.subscription_type+'</td>';
                        html += '<td>'+response.Data.package_addons_id+'</td>';
                        html += '<td>'+response.Data.child_name+'</td>';
                        html += '<td>'+response.Data.child_class+'</td>';
                        html += '<td>'+response.Data.created_at+'</td>';
                        html += '<td>Check</td>';
                        html += '</tr>';

                        $('#subscriptionTable').removeClass('hide');
                        $('#subscriptionTable > tbody').append(html);

                        resetFreeTrialForm(leadId);

                        $('#freeTrialMessage').removeClass('text-red').addClass('text-green').text(response.Message);
                    }else{
                        $('#freeTrialMessage').removeClass('text-green').addClass('text-red').text(response.Message);
                    }
                }
            });
        }
    });
    
    function resetFreeTrialForm(leadId)
    {
        getLearningChildren(leadId);
        $('#selectChild').val('');
        $('#learningChildName').val('');
        $('#learningChildDOB').val('');
        $('#learningChildClass').val('');
        $('#selectPackage').val('');
        $('#startTrialTable').addClass('hide');
    }


    $('body').on('click', '.callLead', function(){
        $('#callBox').removeClass('hide');
        var leadId = $(this).attr('data-val');
        $('#leadActionMessage').html('');
        //$(this).remove();
        //create call history
        $.ajax({
            url     : baseURL+'/admin/ajax/callLead/'+leadId,
            type    : 'post',
            success : function(response)
            {
                $('.callSubmit').attr('data-val', response.Data.id);
                $('.caller-number').html(response.Data.phone);
                $('.called_by').html(response.Data.called_by);
                $('.called_at').html(response.Data.called_at);
                fillLeadStage(response.Data.lead_stage);
                //$('#leadAction').html('');
            }
        });
    });

    $('body').on('click', '.cloudCallLead', function(){
        $('.cloudCallLead').html('Please wait...');
        var leadId = $(this).attr('data-val');
        $('#leadActionMessage').html('');
        //$(this).remove();
        //create call history
        $.ajax({
            url     : baseURL+'/admin/ajax/cloudCallLead/'+leadId,
            type    : 'post',
            success : function(response)
            {
                if(response.Status == '200') {
                    $('#cloudCallStatus').html('Calling...');
                    $('.cloudCallLead').remove();
                    $('#callBox').removeClass('hide');
                    $('.callSubmit').attr('data-val', response.Data.id);
                    $('.caller-number').html(response.Data.phone);
                    $('.called_by').html(response.Data.called_by);
                    $('.called_at').html(response.Data.called_at);
                    fillLeadStage(response.Data.lead_stage);
                    updateCloudCallStatus(response.Data.id);
                }else{
                    $('.cloudCallLead').html('Cloud Call');
                    var html = '<div class="callout callout-danger"><p>'+response.Message+'</p></div>';
                    $('#leadActionMessage').html(html);

                }
                //$('#leadAction').html('');
            }
        });
    });

    function updateCloudCallStatus(callId) {
        // #cloudCallStatus
        cloudCallInterval = setInterval(function(){
            $.ajax({
                url     : baseURL+'/admin/ajax/cloudCallStatus/'+callId,
                type    : 'get',
                async   : false,
                success : function(response)
                {
                    $('#cloudCallStatus').html(response.Message);
                }
            });
            if($('#cloudCallStatus').length == 0){
                clearInterval(cloudCallInterval);
            }
        }, 5000);
    }

    $('body').on('change', '#transfer_lead', function(){
        if($(this).prop('checked')) {
            $('.transferLead').removeClass('hide');
        }else{
            $('.transferLead').addClass('hide');
        }
    });

    $('body').on('click', '.callSubmit', function(){
        var callId = $(this).attr('data-val');
        $('#callSubmitMessage').html('');
        //update call history
        var callAgenda = $('#callAgenda').val();
        var leadStage = $('#leadStage').val();
        var leadStatus = $('#leadStatus').val();
        var leadDeadReason = $('#leadDeadReason').val();
        var note = $('#note').val();
        var scheduleDemoAddress = '';
        var scheduleDemoTime = '';
        var nextFollowUpTime = '';
        var err = false;
        $('#callAgenda').next('div.error').html('');
        $('#leadStatus').next('div.error').html('');

        var scheduleDemo = $('#scheduleDemo').val();
        if(scheduleDemo == '1')
        {
            scheduleDemoAddress = $('#scheduleDemoAddress').val();
            scheduleDemoTime = $('#scheduleDemoTime').val();
        }

        var assignTo = $('#assignTo').val();
        
        var scheduleNextFollowUp = $('#scheduleNextFollowUp').val();
        if(scheduleNextFollowUp == '1')
        {
            nextFollowUpTime = $('#nextFollowUpTime').val();
        }

        if(callAgenda == '')
        {
            err = true;
            $('#callAgenda').next('div.error').html('This is required field.');
        }
        if(leadStatus == '')
        {
            err = true;
            $('#leadStatus').next('div.error').html('This is required field.');
        }
        if(!err)
        {
            $.ajax({
                url     : baseURL+'/admin/ajax/updateCallLead/'+callId,
                type    : 'post',
                async   : false,
                data    : 'callAgenda='+callAgenda+'&leadStage='+leadStage+'&leadStatus='+leadStatus+'&leadDeadReason='+leadDeadReason+'&note='+note+'&scheduleDemoAddress='+scheduleDemoAddress+'&scheduleDemoTime='+scheduleDemoTime+'&nextFollowUpTime='+nextFollowUpTime+'&assignTo='+assignTo,
                success : function(response)
                {
                    if(response.Status == '200') {
                        $('#callBox').addClass('hide');
                        var html = '<div class="callout callout-success"><p>Call history added successfully.</p></div>';
                        $('#leadActionMessage').html(html);
                        $('#historyBox').html(response.Data);
                        clearInterval(cloudCallInterval);
                    }else if(response.Status == '422') {
                        var html = '<div class="callout callout-danger"><p>Call is in progress. Save it after call disconnected.</p></div>';
                        $('#callSubmitMessage').html(html);
                    }
                }
            });
        }
    });

    function fillLeadStage(stage)
    {
        $('.leadStage').removeClass('text-green');
        var i=0;
        for(i;i < stage; i++)
        {
            $('.leadStage').eq(i).addClass('text-green');
        }
        $('#leadStage').val(stage);
        var leadStageMessage = leadStage[stage];
        if(leadStageMessage != '')
        {
            $('.leadStageMessage').removeClass('hide').html(leadStageMessage);
        }else{
            $('.leadStageMessage').addClass('hide').html('');
        }
    }

    $('body').on('change', '#quickNote', function(){
        $('#note').val($(this).val());
    });

    $('body').on('click', '.callAgenda', function(){
        $('.callAgenda').removeClass('btn-success');
        $(this).addClass('btn-success');
        var callAgenda = $(this).attr('data-val');
        $('#callAgenda').val(callAgenda);
    });

    $('body').on('click', '.leadStatus', function(){
        $('.leadStatus').removeClass('btn-success');
        $('.leadStatusDead').removeClass('btn-danger');
        $(this).addClass('btn-success');
        var leadStatus = $(this).attr('data-val');
        $('#leadStatus').val(leadStatus);
        $('#leadDeadReason').val('');
        if(leadStatus == 'no_answer' || leadStatus == 'busy')
        {
            $('.assignTo').removeClass('hide');
        }else{
            $('.assignTo').addClass('hide');
        }
        if(leadStatus == 'not_interested') {
            $('.not_interested_dd').removeClass('hide');
        }else{
            $('#quickNote').val('');
            $('.not_interested_dd').addClass('hide');
        }
    });

    $('body').on('click', '.leadDeadReason', function(){
        $('.leadStatus').removeClass('btn-success');
        $('.leadStatusDead').addClass('btn-danger');
        var leadDeadReason = $(this).attr('data-val');
        $('#leadStatus').val('dead');
        $('#leadDeadReason').val(leadDeadReason);
    });

    //Schedule Demo
    $('body').on('click', '.scheduleDemo', function(){
        $('.schedule-demo').removeClass('hide');
        $('#scheduleDemo').val('1');
    });
    $('body').on('click', '.cancelScheduleDemo', function(){
        $('.schedule-demo').addClass('hide');
        $('#scheduleDemo').val('0');
    });

    //Schedule next follow up
    $('body').on('click', '.scheduleNextFollowUp', function(){
        $('.next-follow-up').removeClass('hide');
        $('#scheduleNextFollowUp').val('1');
    });
    $('body').on('click', '.cancelScheduleNextFollowUp', function(){
        $('.next-follow-up').addClass('hide');
        $('#scheduleNextFollowUp').val('0');
    });

    //Add Note
    $('body').on('click', '.addNote', function(){
        $('#addNoteBox').removeClass('hide');
    });
    
    $('body').on('click', '.cancelLeadNote', function(){
        $('#addNote').val('');
        $('#quickAddNote').val('');
        $('#addNote').next('div.error').html('');
        $('#addNoteBox').addClass('hide');
    });

    $('body').on('change', '#quickAddNote', function(){
        $('#addNote').val($(this).val());
    });

    $('body').on('click', '.saveLeadNote', function(){
        var leadId = $(this).attr('data-val');
        var addNote = $('#addNote').val();
        var err = false;
        $('#addNote').next('div.error').html('');

        if(addNote == '')
        {
            err = true;
            $('#addNote').next('div.error').html('Please enter note here');
        }
        if(!err)
        {
            $.ajax({
                url     : baseURL+'/admin/ajax/addLeadNote/'+leadId,
                type    : 'post',
                data    : 'note='+addNote,
                success : function(response)
                {
                    if(response.Status == '200')
                    {
                        $('#addNote').val('');
                        $('#quickAddNote').val('');
                        $('#addNoteBox').addClass('hide');
                        var html = '<div class="alert alert-success alert-dismissible">';
                        html += '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
                        html += 'Note added to lead successfully.';
                        html += '</div>';
                        $('#leadActionMessage').html(html);
                        $('#historyBox').html(response.Data);
                    }
                }
            });
        }
    });

    //Edit lead
    $('body').on('click', '.editLead', function(){
        var leadId = $(this).attr('data-val');
        $('.lead-value-'+leadId).addClass('hide');
        $('.edit-lead-'+leadId).removeClass('hide');
    });

    $('body').on('click', '.cancelEditLead', function(){
        var leadId = $(this).attr('data-val');
        $('.lead-value-'+leadId).removeClass('hide');
        $('.edit-lead-'+leadId).addClass('hide');
    });
    
    $('body').on('click', '.saveLead', function(){
        var leadId = $(this).attr('data-val');
        var leadName = $('#lead_name-'+leadId).find('.edit-lead-'+leadId).val();
        var leadRelation = $('#lead_relation-'+leadId).find('.edit-lead-'+leadId).val();

        $.ajax({
            url     : baseURL+'/admin/ajax/updateLead/'+leadId,
            type    : 'post',
            data    : 'leadName='+leadName+'&leadRelation='+leadRelation,
            success : function(response)
            {
                if(response.Status == '200')
                {
                    var html = response.Data.name+' <button class="btn '+response.Data.status_class+'">'+response.Data.lead_status+'</button>'
                    $("#showLeadModal").find('.modal-title').html(html);

                    $('#lead_name-'+leadId).find('.lead-value-'+leadId).text(response.Data.name);
                    $('#lead_relation-'+leadId).find('.lead-value-'+leadId).text(response.Data.relation);

                    $('.lead-value-'+leadId).removeClass('hide');
                    $('.edit-lead-'+leadId).addClass('hide');
                }
            }
        });
    });

    //Edit Alternate number
    $('body').on('click', '.editNumber', function(){
        var rowId = $(this).attr('data-val');
        $('.row-value-'+rowId).addClass('hide');
        $('.edit-row-'+rowId).removeClass('hide');
    });

    $('body').on('click', '.cancelEditNumber', function(){
        var rowId = $(this).attr('data-val');
        $('.row-value-'+rowId).removeClass('hide');
        $('.edit-row-'+rowId).addClass('hide');
    });
    
    $('body').on('click', '.saveNumber', function(){
        var rowId = $(this).attr('data-val');
        var numberName = $('#number_name-'+rowId).find('.edit-row-'+rowId).val();
        var numberRelation = $('#number_relation-'+rowId).find('.edit-row-'+rowId).val();

        $.ajax({
            url     : baseURL+'/admin/ajax/updateAlternateNumber/'+rowId,
            type    : 'post',
            data    : 'numberName='+numberName+'&numberRelation='+numberRelation,
            success : function(response)
            {
                if(response.Status == '200')
                {
                    $('#number_name-'+rowId).find('.row-value-'+rowId).text(response.Data.name);
                    $('#number_relation-'+rowId).find('.row-value-'+rowId).text(response.Data.relation);

                    $('.row-value-'+rowId).removeClass('hide');
                    $('.edit-row-'+rowId).addClass('hide');
                }
            }
        });
    });
    
    $('body').on('click', '.sendWhatsAppMsg', function(){
        $('#whatsAppMsgBox').removeClass('hide');
    });

    $('body').on('click', '#cancelSendWaMessage', function(){
        $('#whatsAppMsgBox').addClass('hide');
    });

    $('body').on('change', '#waQuickNote', function(){
        $('#waText').val($(this).val());
    });
    $('body').on('click', '#sendWaMessage', function(){
        var waPhone = $('#waPhone').val();
        var waText = $('#waText').val();
        var location = 'http://wa.me/91'+waPhone+'?text='+encodeURIComponent(waText);
        window.open(location)
    });


    $('body').on('click', '.recordPlay', function(){
        var callID = $(this).attr('data-val');
        var audioElement = document.getElementById('audio-'+callID);
        var action = $(this).text();
        if(action == 'Play')
        {
            $(this).html('Pause');
            $(this).removeClass('btn-success');
            $(this).addClass('btn-danger');
            audioElement.play();
        }else{
            $(this).html('Play');
            $(this).addClass('btn-success');
            $(this).removeClass('btn-danger');
             audioElement.pause();
        }
    });

    $('body').on('click', '.fetchCloudCall', function(){
        var callId = $(this).attr('data-val');
        var leadId = $(this).attr('data-lead');
        $.ajax({
            url     : baseURL+'/admin/ajax/fetchCloudCall/'+callId,
            type    : 'GET',
            success : function(response)
            {
                if(response.Status == '200')
                {
                    $('#historyBox').html(response.Data);
                }
            }
        });
    });
});