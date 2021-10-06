    <div class="row">
        <div class="col-sm-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Lead Detail</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <dl class="dl-horizontal lead-detail">
                        <dt>Last Call</dt>
                        <dd>
                            {{ ($lead->last_call != NULL)? $lead->last_call->format(config('access.date_time_format')) : NULL }}
                        </dd>
                        <dt>Next Follow up</dt>
                        <dd>
                            {{ ($lead->next_follow_up != NULL)? $lead->next_follow_up->format(config('access.date_time_format')) : NULL }}
                        </dd>
                        <dt>Address</dt>
                        <dd>
                            @if($lead->login_id != null && $lead->login_id != 0)
                                @if($address != '')
                                    {{ $address }}
                                @else
                                    <span class="userAddress"></span>
                                    <a href="javascript:void(0)" id="addAddress">Add Address</a>
                                    <div class="row add-address hide">
                                        <div class="col-md-8">
                                            <input type="text" class="form-control input-sm" id="txtUserAddress">
                                            <input type="hidden" id="txtUserLatLng">
                                            <input type="hidden" id="txtUserLocality">
                                            <input type="hidden" id="txtUserCity">
                                            <input type="hidden" id="txtUserState">
                                            <input type="hidden" id="txtUserCountry">
                                        </div>
                                        <div class="col-md-4" style="padding-top:3px;">
                                            <button class="btn btn-success btn-xs"  id="updateAddress" data-val="{{ $lead->id }}">Save</button>
                                            <button class="btn btn-danger btn-xs" id="cancelAddAddress">Cancel</button>
                                        </div>
                                    </div>
                                @endif   
                            @else
                                User is not on platform.
                            @endif
                        </dd>
                    </dl>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
        <div class="col-sm-6">
            <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    <div id="alternateNumberMsg"></div>
                    <table class="table table-condensed table-bordered" id="leadDetail">
                        <tbody>
                        <tr>
                            <th style="width:60px;">Primary</th>
                            <th>Phone No.</th>
                            <th>Name</th>
                            <th>Relation</th>
                            <th>Action</th>
                        </tr>
                        <tr>
                            <td class="{{ ($lead->preferred == '1')? 'text-green' : '' }} primary-number">
                                <span data-type="lead" data-val="{{ $lead->id}}" data-lead="{{ $lead->id}}">
                                    <i class="fa fa-check-circle-o"></i>
                                </span>
                            </td>
                            <td>{{ $lead->phone }}</td>
                            <td id="lead_name-{{ $lead->id }}">
                                <span class="lead-value-{{ $lead->id }}">{{ $lead->name }}</span>
                                <input type="text" value="{{ $lead->name }}" class="form-control input-sm hide edit-lead-{{ $lead->id }}">
                            </td>
                            <td id="lead_relation-{{ $lead->id }}">
                                <span class="lead-value-{{ $lead->id }}">{{ $lead->relation }}</span>
                                <input type="text" value="{{ $lead->relation }}" class="form-control input-sm hide edit-lead-{{ $lead->id }}">
                            </td>
                            <td>
                                <span class="lead-value-{{ $lead->id }}">
                                    <button class="btn btn-xs btn-primary editLead" data-val="{{ $lead->id }}"><i class="fa fa-pencil"></i></button>
                                </span>
                                <span class="hide edit-lead-{{ $lead->id }}">
                                    <button class="btn btn-xs btn-success saveLead" data-val="{{ $lead->id }}"><i class="fa fa-check"></i></button>
                                    <button class="btn btn-xs btn-danger cancelEditLead" data-val="{{ $lead->id }}"><i class="fa fa-times"></i></button>
                                </span>
                            </td>
                        </tr>
                        @if($lead->alternateNumbers()->count() > 0)
                            @foreach($lead->alternateNumbers as $row)
                            <tr>
                                <td class="{{ ($row->preferred == '1')? 'text-green' : '' }} primary-number">
                                    <span data-type="alternate" data-val="{{ $row->id}}" data-lead="{{ $row->lead_id}}">
                                        <i class="fa fa-check-circle-o"></i>
                                    </span>
                                </td>
                                <td>{{ $row->phone }}</td>
                                <td id="number_name-{{ $row->id }}">
                                    <span class="row-value-{{ $row->id }}">{{ $row->name }}</span>
                                    <input type="text" value="{{ $row->name }}" class="form-control input-sm hide edit-row-{{ $row->id }}">
                                </td>
                                <td id="number_relation-{{ $row->id }}">
                                    <span class="row-value-{{ $row->id }}">{{ $row->relation }}</span>
                                    <input type="text" value="{{ $row->relation }}" class="form-control input-sm hide edit-row-{{ $row->id }}">
                                </td>
                                <td>
                                    <span class="row-value-{{ $row->id }}">
                                        <button class="btn btn-xs btn-primary editNumber" data-val="{{ $row->id }}"><i class="fa fa-pencil"></i></button>
                                        <button class="btn btn-xs btn-danger" data-toggle="an-confirmation" data-value="{{ $row->id }}" data-singleton="true">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </span>
                                    <span class="hide edit-row-{{ $row->id }}">
                                        <button class="btn btn-xs btn-success saveNumber" data-val="{{ $row->id }}"><i class="fa fa-check"></i></button>
                                        <button class="btn btn-xs btn-danger cancelEditNumber" data-val="{{ $row->id }}"><i class="fa fa-times"></i></button>
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <a href="javascript:void(0)" id="toggleAlternateNumber">Add Alternate Number</a>
                        </div>
                    </div>
                    <div class="row alternate-number hide">
                        <div class="col-md-3">
                            <input class="form-control masking" type="text" placeholder="Phone Number" id="alertnatePhone" data-inputmask='"mask": "99999-99999"' data-mask minlength="11" maxlength="11">
                            <div class="error"></div>
                        </div>
                        <div class="col-md-3">
                            <input class="form-control" type="text" placeholder="Name" id="alertnatePhoneName">
                            <div class="error"></div>
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" type="text" placeholder="Relation" id="alertnatePhoneRelation">
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-info saveAlternateNumber" data-val="0" data-lead="{{ $lead->id }}">Save</button>
                            <button class="btn btn-success saveAlternateNumber" data-val="1" data-lead="{{ $lead->id }}">Save as Preferred</button>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
    </div>

    <!-- Child List -->
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Child Data</h3>
            <div class="box-tools pull-right" style="margin-top:8px;">
                <button type="button" class="btn btn-success btn-xs addChildBtn hide">
                    <i class="fa fa-plus"></i> Add Child
                </button>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table class="table table-condensed table-bordered" id="childTable">
            <tbody>
            <tr>
                <th>Child Name</th>
                <th>Gender</th>
                <th>Age</th>
                <th>Class</th>
            </tr>
            @if($children)
                @foreach($children as $child)
                <tr>
                    <td id="child_name-{{ $child->id }}">
                        <span class="child-value-{{ $child->id }}">{{ $child->name }}</span>
                        <input type="text" value="{{ $child->name }}" class="form-control input-sm hide edit-child-{{ $child->id }}">
                    </td>
                    <td id="child_gender-{{ $child->id }}">
                        <span class="child-value-{{ $child->id }}">{{ $child->gender }}</span>
                        <select class="form-control input-sm hide edit-child-{{ $child->id }}">
                            <option value="">Select Gender</option>
                            <option value="BOY" {!! ($child->gender == 'BOY')? 'selected="selected"' : '' !!}>Boy</option>
                            <option value="GIRL" {!! ($child->gender == 'GIRL')? 'selected="selected"' : '' !!}>Girl</option>
                        </select>
                    </td>
                    <td id="child_dob-{{ $child->id }}">
                        <span class="child-value-{{ $child->id }}">
                            @php
                            $age = \Carbon\Carbon::parse($child->dob)->age;    
                            @endphp
                            {{ ($age != null && $age != 0)? $age.' '.trans_choice('strings.backend.general.years', $age): NULL }}
                        </span>
                        {{-- <input type="text" value="{{ ($child->dob !=null)? $child->dob->format('d/m/Y') : NULL }}" class="form-control input-sm datemask hide edit-child-{{ $child->id }}" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask> --}}
                    </td>
                    <td id="child_class-{{ $child->id }}">
                        <span class="child-value-{{ $child->id }}">{{ getClassGradeName($child->child_class_id) }}</span>
                        <select class="form-control input-sm hide edit-child-{{ $child->id }}">
                            <option value="1" {!! ($child->child_class_id == '1')? 'selected="selected"':'' !!}>Play Group</option>
                            <option value="2" {!! ($child->child_class_id == '2')? 'selected="selected"':'' !!}>Nursery</option>
                            <option value="3" {!! ($child->child_class_id == '3')? 'selected="selected"':'' !!}>Lower/Junior KG</option>
                            <option value="4" {!! ($child->child_class_id == '4')? 'selected="selected"':'' !!}>Upper/Senior KG</option>
                            <option value="18" {!! ($child->child_class_id == '18')? 'selected="selected"':'' !!}>Home Taught</option>
                        </select>
                    </td>
                    {{-- <td>{{ ucfirst($child->data_medium) }}</td>
                    <td id="child_school-{{ $child->id }}">
                        <span class="child-value-{{ $child->id }}">{{ $child->school_name }}</span>
                        <input type="text" value="{{ $child->school_name }}" class="form-control input-sm hide edit-child-{{ $child->id }}">
                    </td>
                    <td>{{ ($child->added_on != NULL)? $child->added_on->format(config('access.date_format')) : NULL }}</td>
                    <td>
                        <span class="child-value-{{ $child->id }}">
                            <button class="btn btn-xs btn-primary editChid" data-val="{{ $child->id }}"><i class="fa fa-pencil"></i></button>
                            <button class="btn btn-xs btn-danger" data-toggle="confirmation" data-value="{{ $child->id }}" data-singleton="true">
                                <i class="fa fa-trash"></i>
                            </button>
                        </span>
                        <span class="hide edit-child-{{ $child->id }}">
                            <button class="btn btn-xs btn-success saveChild" data-val="{{ $child->id }}"><i class="fa fa-check"></i></button>
                            <button class="btn btn-xs btn-danger cancelEditChild" data-val="{{ $child->id }}"><i class="fa fa-times"></i></button>
                        </span>
                    </td> --}}
                </tr>
                @endforeach
            @endif
            <tr id="addChildTr" class="hide">
                <td>
                    <input type="text" id="child_name" class="form-control input-sm">
                </td>
                <td>
                    <select class="form-control input-sm" id="child_gender">
                        <option value="">Select Gender</option>
                        <option value="BOY">Boy</option>
                        <option value="GIRL">Girl</option>
                    </select>
                </td>
                <td>
                    <input type="text" id="child_dob" class="form-control input-sm datemask" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                </td>
                <td>
                    <select class="form-control input-sm" id="child_class">
                        <option value="1">Play Group</option>
                        <option value="2">Nursery</option>
                        <option value="3">Lower/Junior KG</option>
                        <option value="4">Upper/Senior KG</option>
                        <option value="18">Home Taught</option>
                    </select>
                </td>
                <td>Call</td>
                <td>
                    <input type="text" class="form-control input-sm" id="child_school">
                </td>
                <td></td>
                <td>
                    <button class="btn btn-xs btn-success addChild" data-val="{{ $lead->id }}"><i class="fa fa-check"></i></button>
                    <button class="btn btn-xs btn-danger cancelAddChild"><i class="fa fa-times"></i></button>
                </td>
            </tr>
            </tbody>
            </table>
        </div>
        <!-- /.box-body -->
    </div>

    <!-- Lead Stage -->
    {{-- <div class="box">
        <div class="box-header">
            <h3 class="box-title">Lead Stage</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table class="table table-condensed table-bordered">
            <tbody>
            <tr>
                @if($lead->messenger == 1)
                    <td class="text-green"><i class="fa fa-circle-o"></i> Messenger Active</td>
                @else
                    <td><i class="fa fa-circle-o"></i> Messenger Inactive</td>
                @endif
                @if($lead->learning == 1)
                    <td class="text-green"><i class="fa fa-circle-o"></i> Learning Active</td>
                @else
                    <td><i class="fa fa-circle-o"></i> Learning Inactive</td>
                @endif
                <td {!! ($lead->lead_stage >= 3)? 'class="text-green"':'' !!}><i class="fa fa-circle-o"></i> Trial</td>
                <td {!! ($lead->lead_stage >= 4)? 'class="text-green"':'' !!}><i class="fa fa-circle-o"></i> Upgrade</td>
            </tr>
            </tbody>
            </table>
        </div>
    </div> --}}

    <!-- Learning Subscription -->
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Learning Data</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="text-center {{ ($lead->learning == '0' && $lead->learning_id == '0')? '': 'hide'}}">
                <button class="btn btn-success" id="activateLearning" data-val="{{ $lead->id }}">Activate Learning</button>
            </div>
            <table class="table table-condensed table-bordered {{ (($lead->learning == '0' && $lead->learning_id == '0') || $subscriptions->count() == '0')? 'hide': ''}}" id="subscriptionTable">
            <tbody>
            <tr>
                <th>Subscription</th>
                <th>Type</th>
                <th>Physical</th>
                <th>Child</th>
                <th>Class</th>
                <th>Date</th>
                <th>Activity Check</th>
            </tr>
            @if($subscriptions->count() != 0)
                @foreach($subscriptions as $subscription)
                    <tr>
                        <td>{{ $subscription->package_name }}</td>
                        <td>{{ $subscription->subscription_type }}</td>
                        <td>{{ ($subscription->package_addons_id == '0')? 'No': 'Yes' }}</td>
                        <td>{{ $subscription->child_name }}</td>
                        <td>{{ getClassGradeName($subscription->child_class) }}</td>
                        <td>{{ date(config('access.date_format'), strtotime($subscription->created_at))}}</td>
                        <td>Check</td>
                    </tr>
                @endforeach
            @endif
            </tbody>
            </table>
            <br>

        <a href="javascript:void(0)" id="startFreeTrialToggle" class="btn btn-success {{ ($lead->learning == '0' && $lead->learning_id == '0')? 'hide': ''}}">Start Free Trial</a>
            <table class="table table-condensed table-bordered hide" id="startTrialTable">
            <tbody>
            <tr>
                <td>
                    <select id="selectChild" class="form-control">
                        <option value="">Select Child</option>
                    </select>
                </td>
                <td>
                    <input type="text" placeholder="Child Name" class="form-control" id="learningChildName">
                    <div class="error"></div>
                </td>
                <td>
                    <input type="text" id="learningChildDOB" class="form-control datemask" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                </td>
                <td>
                    <select class="form-control" id="learningChildClass">
                        <option value="">Select Class</option>
                        <option value="1">Play Group</option>
                        <option value="2">Nursery</option>
                        <option value="3">Lower/Junior KG</option>
                        <option value="4">Upper/Senior KG</option>
                        <option value="18">Home Taught</option>
                    </select>
                </td>
                <td>
                    <select id="selectPackage" class="form-control">
                        <option value="">Select Package</option>
                        @if($packages)
                            @foreach($packages as $package)
                                <option value="{{ $package->id }}">{{ $package->package_name}}</option>
                            @endforeach
                        @endif
                    </select>
                    <div class="error"></div>
                </td>
                <td>
                <button class="btn btn-success" id="startTrialSubmit" data-val="{{ $lead->id }}">Start Trial</button>
                    <button class="btn btn-danger cancelStartTrial">Cancel</button>
                </td>
            </tr>
            </tbody>
            </table>
            <div class="text-red text-center" id="freeTrialMessage"></div>
        </div>
        <!-- /.box-body -->
    </div>

    <!-- Lead History -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Lead History</h3>
        </div>
        <div class="box-body" id="historyBox">
            {!! history()->renderEntity('Lead', $lead->id, null, false) !!}
        </div>
    </div>


    <div id="leadAction">
        <div class="row">
            <div class="col-md-8 col-md-offset-2" >
                <div class="row">
                    <div class="col-md-3">
                        <button class="btn btn-success btn-block refreshLead" data-val="{{ $lead->id }}">Refresh Lead</button>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-info btn-block sendWhatsAppMsg">Send WhatsApp Message</button>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-info btn-block addNote">Add Note</button>
                    </div>
                    @if($lead->assigned_to == $user->id)
                        <div class="col-md-2">
                            <button class="btn btn-success btn-block callLead" data-val="{{ $lead->id }}">Call Lead</button>
                        </div>
                        @if($user->phone != NULL && $user->phone != '')
                        <div class="col-md-2">
                            <button class="btn btn-warning btn-block cloudCallLead" data-val="{{ $lead->id }}">Cloud Call</button>
                        </div>
                        @endif
                    @endif
                   
                </div>
                <div id="cloudCallStatus"></div>
                <div id="leadActionMessage"></div>
            </div>
            <br><br>
        </div>
    </div>
    <br>

    @if($lead->assigned_to == $user->id)
    <!-- Call Lead Box -->
    <div class="row hide" id="callBox">
        <div class="col-md-8 col-md-offset-2">
            <div class="box">
                <div class="box-body">
                    <h1 class="text-center caller-number"></h1>
                    <dl class="dl-horizontal">
                        <div class="row">
                            <div class="col-md-6">
                                <dt class="text-right">Call By</dt>
                                <dd class="called_by">John Doe</dd>
                            </div>
                            <div class="col-md-6">
                                <dt class="text-right">Time</dt>
                                <dd class="called_at">John Doe</dd>
                            </div>
                        </div>
                    </dl>
                    <dl class="dl-full">
                        <dt>Call Agenda</dt>
                        <dd>
                            <button class="callAgenda btn btn-lg mar20r" data-val="training"=>TRAINING</button>
                            <button class="callAgenda btn btn-lg" data-val="sale">SALES</button>
                            <input type="hidden" id="callAgenda">
                            <div class="error"></div>
                        </dd>
                        <dt>Lead Stage</dt>
                        <dd>
                            <input type="hidden" id="leadStage">
                            <table class="table table-condensed lead-stage">
                                <tbody>
                                <tr>
                                    <td><span class="leadStage" data-val="1"><i class="fa fa-circle-o"></i> Messenger</span></td>
                                    <td><span class="leadStage" data-val="2"><i class="fa fa-circle-o"></i> Learning Activated</span></td>
                                    {{-- <td><span class="leadStage" data-val="3"><i class="fa fa-circle-o"></i> Trial</span></td>
                                    <td><span class="leadStage" data-val="4"><i class="fa fa-circle-o"></i> Upgrade</span></td> --}}
                                </tr>
                                </tbody>
                            </table>
                            <div class="alert alert-warning leadStageMessage hide"></div>
                        </dd>
                        <dt>Lead Status</dt>
                        <dd>
                            <button class="leadStatus btn btn-lg mar20r" data-val="sale">SALE</button>
                            <button class="leadStatus btn btn-lg mar20r" data-val="hot">HOT</button>
                            <button class="leadStatus btn btn-lg mar20r" data-val="mild">MILD</button>
                            <button class="leadStatus btn btn-lg mar20r" data-val="cold">COLD</button>
                            <button class="leadStatus btn btn-lg mar20r" data-val="no_answer">NO ANSWER</button>
                            <button class="leadStatus btn btn-lg mar20r" data-val="busy">BUSY</button>
                            <button class="leadStatus btn btn-lg mar20r" data-val="not_interested">NOT INTERESTED</button>
                            <button class="leadStatus btn btn-lg" data-val="dead">DEAD</button>

                            {{-- <div class="btn-group pull-right">
                                <button type="button" class="leadStatusDead btn btn-lg">DEAD</button>
                                <button type="button" class="leadStatusDead btn btn-lg dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="#" class="leadDeadReason" data-val="1">Reason 1</a></li>
                                    <li><a href="#" class="leadDeadReason" data-val="2">Reason 2</a></li>
                                    <li><a href="#" class="leadDeadReason" data-val="3">2 Years Old Leads</a></li>
                                </ul>
                            </div> --}}
                            <input type="hidden" id="leadDeadReason">
                            <input type="hidden" id="leadStatus">
                            <div class="error"></div>
                        </dd>
                        <dd class="assignTo hide">
                            <input type="checkbox" id="transfer_lead"/> 
                            <label for="transfer_lead">Transfer this lead to other executive.</label>
                        </dd>
                        <dt class="transferLead hide">Assign To</dt>
                        <dd class="transferLead hide">
                            <select id="assignTo" class="form-control">
                                <option value="">Select Executive</option>
                                @foreach($executives as $key=>$executive)
                                    <option value="{{ $key }}">{{ $executive }}</option>
                                @endforeach
                            </select>
                        </dd>
                        <dt>Notes</dt>
                        <dd>
                            <textarea id="note" cols="30" rows="2" class="form-control"></textarea>
                        </dd>
                        <dt>Quick Notes</dt>
                        <dd>
                            <select id="quickNote" class="form-control">
                                <option value="">Select Quick Note</option>
                                <option>Not reachable</option>
                                <option>Wrong Number</option>
                                <option class="not_interested_dd hide">Due to Price</option>
                                <option class="not_interested_dd hide">Child not right age</option>
                            </select>
                        </dd>
                        <dt>
                            <a href="javascript:void(0)" class="scheduleDemo">
                                Schedule Demo
                            </a>
                        </dt>
                        <dd>
                            <input type="hidden" id="scheduleDemo" value="0">
                            <div class="row schedule-demo hide">
                                <div class="col-md-4">
                                    <label for="">Address</label>
                                    <textarea id="scheduleDemoAddress" cols="20" rows="2" class="form-control"></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label for="">Date - Time</label>
                                    <br>
                                    <input type="text" id="scheduleDemoDatePicker" class="form-control">
                                    <input type="hidden" id="scheduleDemoTime">
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-danger cancelScheduleDemo pull-right">Cancel</button>
                                </div>
                            </div>
                        </dd>
                        <dt>
                            <a href="javascript:void(0)" class="scheduleNextFollowUp">
                                Schedule Next Follow Up
                            </a>
                        </dt>
                        <dd>
                            <input type="hidden" id="scheduleNextFollowUp" value="0">
                            <div class="row hide next-follow-up">
                                <div class="col-md-4">
                                    <label for="">Date - Time</label>
                                    <br>
                                    <input type="text" id="nextFollowUpTimePicker" class="form-control">
                                    <input type="hidden" id="nextFollowUpTime">
                                </div>
                                <div class="col-md-8">
                                    <button class="btn btn-danger cancelScheduleNextFollowUp pull-right">Cancel</button>
                                </div>
                            </div>
                        </dd>
                    </dl>
                    <div class="col-md-4 col-md-offset-4">
                        <div id="callSubmitMessage"></div>
                        <button class="btn btn-success btn-block callSubmit">Save</button>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
    </div>
    @endif

    <!-- Add Note -->
    <div class="box hide" id="addNoteBox">
        <div class="box-body">
            <dl class="dl-full">
                <dt>Notes</dt>
                <dd>
                    <textarea id="addNote" cols="30" rows="2" class="form-control"></textarea>
                    <div class="error"></div>
                </dd>
                <dt>Quick Notes</dt>
                <dd>
                    <select id="quickAddNote" class="form-control">
                        <option value="">Select Quick Note</option>
                        <option>Not reachable</option>
                        <option>Wrong Number</option>
                    </select>
                </dd>
            </dl>
            <div class="col-md-4 col-md-offset-4 text-center">
                <button class="btn btn-success saveLeadNote" data-val="{{ $lead->id }}">Save</button>
                <button class="btn btn-danger cancelLeadNote">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Send WhatsApp Message -->
    <div class="row hide" id="whatsAppMsgBox">
        <div class="col-md-6 col-md-offset-3">
            <div class="box">
                <div class="box-body">
                    <form class="form-horizontal">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="waPhone" class="col-sm-2 control-label">Phone</label>
                                <div class="col-sm-10">
                                    <input type="text" name="phone" class="form-control" id="waPhone" placeholder="Phone" value="{{ leadPhone($lead->phone) }}"
                                    minlength="10" maxlength="10">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"></label>
                                <div class="col-sm-10">
                                    <select id="waQuickNote" class="form-control">
                                        <option value="">Select Quick Note</option>
                                        <option value="Introducing you ClassMonitor Learning Platform. Hands on after school learning kit for your young ones. 
Check the introduction here:  https://youtu.be/YEtxPlZSkIc 
Download the Free application now: http://classmonitor.app
                                        ">Introduction Video</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="waText" class="col-sm-2 control-label">Message</label>
                                <div class="col-sm-10">
                                    <textarea name="text" id="waText" rows="5" class="form-control"></textarea>
                                </div>
                            </div>
                            
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <button type="button" class="btn btn-danger" id="cancelSendWaMessage">Cancel</button>
                            <button class="btn btn-info pull-right" id="sendWaMessage">Send</button>
                        </div>
                        <!-- /.box-footer -->
                    </form>
                </div>
            </div>
        </div>
    </div>
 