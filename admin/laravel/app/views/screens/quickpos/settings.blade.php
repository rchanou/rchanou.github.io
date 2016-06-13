@extends('master')

@section('title')
QuickPOS Settings
@stop

@section('css_includes')
    @parent
    {{ HTML::style('css/jquery.ui.ie.css') }}
    {{ HTML::style('css/jquery-ui.css') }}
    <style>
        .select2-container-multi .select2-choices .select2-search-choice {
            color: black; background-color: white;
        }
    </style>
@stop

@section('pageHeader')
QuickPOS Settings
@stop

@section('breadcrumb')
<a href="{{URL::to('dashboard')}}" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
<a href="#">QuickPOS</a>
<a href="#" class="current">Settings</a>
@stop

@section('content')
{{ Form::open(array('action'=>'QuickPOSController@updateSettings','files'=>false, 'class' => 'form-horizontal')) }}

    <div class="container-fluid">
      <div class="row">
          <div class="col-xs-12">
            @if (Session::has("message"))
            <div class="alert alert-success fadeAway">
                <p>{{ Session::get("message") }}</p>
            </div>
            @endif
            @if (Session::has("error"))
            <div class="alert alert-danger">
                <p>{{ Session::get("error") }}</p>
            </div>
            @endif
            @if (Session::has("errors"))
            <div class="alert alert-danger"> <!-- Errors from Laravel validation -->
                <ul>
                @foreach ($errors->all('<li>:message</li>') as $message)
                    {{ $message }}
                @endforeach
                </ul>
            </div>
            @endif

            <div class="widget-box">
              <div class="widget-title">
                <span class="icon">
                  <i class="fa fa-align-justify"></i>
                </span>
                <h5>General Settings</h5>
              </div>
              <div class="widget-content">
                  <div class="row">
                      @if(isset($quickPOSSettings) && count($quickPOSSettings) > 0)
                          <div class="col-sm-6">
                             @if(isset($quickPOSSettings['QuickPOSAddCustomer']))
                             <div class="form-group">
                                 <label class="col-sm-4 col-md-4 col-lg-4 control-label">Add Customer By Name?</label>
                                 <div class="col-sm-8 col-md-8 col-lg-8">
                                     <input type="checkbox" id="QuickPOSAddCustomer" name="QuickPOSAddCustomer" {{$isChecked['QuickPOSAddCustomer']}}>
                                     <span class="help-block text-left">If checked, a customer can be added to the race with just their first and last name, even if they do not yet have an account. A new one is created on the spot!</span>
                                 </div>
                             </div>
                             @endif
                             @if(isset($quickPOSSettings['QuickPOSDefaultCategoryId']))
                             <div class="form-group">
                                 <label class="col-sm-4 col-md-4 col-lg-4 control-label">Default Category</label>
                                 <div class="col-sm-8 col-md-8 col-lg-8">
                                     <select name="QuickPOSDefaultCategoryId" id="QuickPOSDefaultCategoryId" value="{{$quickPOSSettings['QuickPOSDefaultCategoryId']}}">
                                         @foreach($categories as $category)
                                             <option value="{{$category->categoryId}}" @if($category->categoryId == $quickPOSSettings['QuickPOSDefaultCategoryId'])selected="selected"@endif>
                                                 {{$category->description}}
                                             </option>
                                         @endforeach
                                     </select>
                                     <span class="help-block text-left">The default category of products that is shown in the central pane when the QuickPOS is opened.</span>
                                 </div>
                             </div>
                             @endif
                          </div>
                          <div class="col-sm-6">
                               @if(isset($quickPOSSettings['QuickPOSTrackNumbers']))
                               <div class="form-group">
                                   <label class="col-sm-4 col-md-4 col-lg-4 control-label">Tracks Shown</label>
                                   <div class="col-sm-8 col-md-8 col-lg-8">
                                       <select style="width: 100%;" multiple="multiple" name="QuickPOSTrackNumbers[]" id="QuickPOSTrackNumbers" value="{{$quickPOSSettings['QuickPOSTrackNumbers']}}">
                                           @foreach($tracks as $track)
                                               <option value="{{$track->id}}" @if(in_array($track->id,$trackIds))selected="selected"@endif>
                                                   {{$track->name}}
                                               </option>
                                           @endforeach
                                       </select>
                                       <span class="help-block text-left">Which tracks to show in the track scheduler in the left pane. <br/>Leave blank for all tracks.</span>
                                   </div>
                               </div>
                               @endif
                          </div>
                          <div class="col-sm-12">
                              <div class="alert alert-warning">
                                  Note: After updating these settings, you must restart Main Engine.<br><br>

                                  To manage categories, tracks, heat types, or products, please proceed to <a href="{{'http://' . $_SERVER['HTTP_HOST'] . '/sp_admin'}}" target="_blank">sp_admin</a>.
                              </div>
                          </div>
                          <div class="col-sm-12">
                               <div class="form-actions" style="margin-bottom: 10px;">
                                   {{ Form::submit('Save Changes', array('class' => 'btn btn-info')) }}
                               </div>
                          </div>
                      @else
                          <div class="col-sm-12">
                              <div class="alert alert-warning">
                                  <p>This feature needs to be enabled by Club Speed. Please contact us for assistance.</p>
                              </div>
                          </div>
                      @endif
                  </div>
              </div>
            </div>

            <div class="widget-box">
                <div class="widget-title">
                    <span class="icon">
                      <i class="fa fa-align-justify"></i>
                    </span>
                    <h5>Products for Heat Types</h5>
                    </div>
                    <div class="widget-content">
                        <div class="row">
                            @if($heatTypesMigrationWasRun)
                                @foreach($heatTypes as $heatType)
                                    @if(isset($trackNames[$heatType->trackId]))
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="col-sm-4 col-md-4 col-lg-4 control-label">{{$heatType->name}}
                                        <br><small><em>{{$trackNames[$heatType->trackId]}}&nbsp;</em></small>
                                        </label>
                                        <div class="col-sm-8 col-md-8 col-lg-8">
                                            <label class="control-label">Default Product: </label>
                                            <select class="productSelect" style="width: 100%;" name="defaultProductForHeatTypesId-{{$heatType->heatTypesId}}" value="{{isset($heatType->productId) ?: ""}}">
                                                <option value="null">None</option>
                                                @foreach($products as $product)
                                                    @if($product->productId == $heatType->productId)
                                                    <option value="{{$product->productId}}" @if($product->productId == $heatType->productId)selected="selected"@endif>
                                                        {{$product->description}}
                                                    </option>
                                                    @endif
                                                @endforeach
                                            </select><br/>
                                            @if($heatTypeProductsTableExists)
                                            <label class="control-label">Alternate Products: </label>
                                            <select class="productSelectAlternate" style="width: 100%;" multiple name="alternateProductsForHeatTypesId-{{$heatType->heatTypesId}}[]" value="">
                                                @foreach($products as $product)
                                                    @if(isset($heatTypeProducts[$heatType->heatTypesId][$product->productId]))
                                                    <option value="{{$product->productId}}" @if(isset($heatTypeProducts[$heatType->heatTypesId][$product->productId]))selected="selected"@endif>
                                                        {{$product->description}}
                                                    </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                    @endif
                                @endforeach
                                <div class="col-sm-12">
                                    <div class="alert alert-info">
                                        These settings allow you to pair heat types with default products.
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-actions" style="margin-bottom: 10px;">
                                        {{ Form::submit('Save Changes', array('class' => 'btn btn-info')) }}
                                    </div>
                                </div>
                            @else
                                <div class="col-sm-12">
                                    <div class="alert alert-warning">
                                        <p>This feature needs to be enabled by Club Speed. Please contact us for assistance.</p>
                                    </div>
                                    @if(Session::has('user') && strtolower(Session::get('user')) == 'support')
                                        <div class="alert alert-info">
                                            <img src="{{asset('img/support_only.png')}}" style="cursor: help" title="This information is only visible to Club Speed support staff.">&nbsp;
                                            Run <a href="{{'http://' . $_SERVER['HTTP_HOST'] . '/api/migrations/201602241100 - Add HeatTypes ProductID.php'}}" target="_blank">this migration</a> to enable this section for the customer. The API must be up to date.
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

          </div>
     </div>

    {{ Form::close() }}

    </div>

@stop

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
@parent
<script>

    $(document).ready(function () {

        window.setTimeout(function () {
            $(".fadeAway").fadeTo(500, 0).slideUp(500, function () {
                $(this).remove();
            });
        }, 5000);

        var products = {{json_encode($products)}};
        var productsData = products.map(function (element) {
            return {id: element.productId, text: element.description};
        });

        $(".productSelect").one('select2:opening', function (e) {
            var $this = $(this);
            $this.select2({
                data: productsData
            }).trigger('change');
            setTimeout(function(){
                $this.select2("open");
            }, 100);
        });

        $(".productSelectAlternate").one('select2:opening', function (e) {
            var $this = $(this);
            $this.select2({
                data: productsData
            }).trigger('change');
            setTimeout(function(){
                $this.select2("open");
            }, 100);
        });
    });

</script>
@stop
<!-- END JAVASCRIPT INCLUDES -->