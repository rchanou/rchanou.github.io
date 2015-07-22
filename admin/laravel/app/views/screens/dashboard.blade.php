@extends('master')

@section('title')
Dashboard
@stop

@section('pageHeader')
Dashboard
@stop

@section('breadcrumb')
    <a href="{{URL::to('dashboard')}}" title="Go to Home" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
@stop

@section('content')


<div class="row">
   <div class="col-md-12" style="text-align:center;">
    <img src="http://www.clubspeedtiming.com/img/whatsnew2.png"/></br>
   </div>
</div>


<div class="row">
	<div class="col-xs-12 col-sm-1 col-md-2 col-lg-3"></div>
	<div class="col-xs-12 col-sm-10 col-md-8 col-lg-6">
		<div class="widget-box">
			<div class="widget-title">
				<span class="icon">
					<i class="fa fa-th-list"></i>
				</span>
				<h5>Solve common business challenges</h5>
			</div>
			<div class="widget-content" style="text-align: center;">
				<iframe width="100%" height="315" style="max-width:560px;" src="https://www.youtube.com/embed/BRRZc97Eiwo" frameborder="0" allowfullscreen></iframe>
				<p>
					In this webinar we will show you how Club Speed's new EMV, Affiliate Tracking, SpeedText and New Registration can help you solve and automate the most common karting business challenges.
				</p>
			</div>
		</div>
	</div>
	<div class="col-xs-12 col-sm-1 col-md-2 col-lg-3"></div>
</div>
<div class="row">
					<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
						<div class="widget-box">
							<div class="widget-title">
								<span class="icon">
									<i class="fa fa-th-list"></i>
								</span>
								<h5>New Feature Highlights</h5>
							</div>
							<div class="widget-content" style="text-align: center;">
<iframe width="100%" height="315" style="max-width:560px;"  src="https://www.youtube.com/embed/tSgZL0L_ATU" frameborder="0" allowfullscreen></iframe>
<p>Watch this webinar to see our newest features in action. eGiftcards, the Mobile app for iOS and Android, Speed Screen HD and the new Enhanced Facebook integration.</p>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
						<div class="widget-box">
							<div class="widget-title">
								<span class="icon">
									<i class="fa fa-th-list"></i>
								</span>
								<h5>Automate your Business</h5>
							</div>
							<div class="widget-content" style="text-align: center;">
<iframe width="100%" height="315" style="max-width:560px;" src="https://www.youtube.com/embed/He8B49xZ49c" frameborder="0" allowfullscreen></iframe>
<p>
         See how Club Speed can help lower your overhead and increase your customer's satisfaction with these 6 automation tools.
        </p>
							</div>
						</div>
					</div>
				</div>
<div class="row">
   <div class="col-md-12">
      
      <div class="alert alert-info">
         <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <strong><span class="glyphicon glyphicon-link"></span> Did You Know?</strong></br>
                 <p>
                    We are continually adding and building upon our knowledge base articles! Everything from setup, running races and leagues, to marketing emails and Facebook integration setup can be read about on our searchable training site. Click the button below and check it out!</br>
                     </br>
             <div style="text-align:center;">
                  <a href="http://clubspeed.zendesk.com/hc/" class="btn btn-primary">Club Speed Knowledge Base</a>
             </div>
        </p>
        
      </div>
   </div>
</div>

@stop