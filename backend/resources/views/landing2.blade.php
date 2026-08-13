
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ s('title') }}</title>
    @include('components.seo-header')

    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>

        <link rel="stylesheet" type="text/css" href="https://storage.perfectcdn.com/2x0a15/kwgsay42dy8mp1bc.css">
        <link rel="stylesheet" type="text/css" href="https://storage.perfectcdn.com/2x0a15/d41nqabtgp20geu9.css">
        <link rel="stylesheet" type="text/css" href="https://storage.perfectcdn.com/libs/bootstrap-datetimepicker/4.17.47/bootstrap-datetimepicker.min.css">
        <link rel="stylesheet" type="text/css" href="https://storage.perfectcdn.com/css/font-awesome/css/all.min.css">
        <link rel="stylesheet" type="text/css" href="https://storage.perfectcdn.com/global/w1ak29btsbdhu8s0.css">
        <link rel="stylesheet" type="text/css" href="https://storage.perfectcdn.com/global/cao32di3zo8u7xgq.css">
      </head>
  <body class="guest">
        <nav class="navbar navbar-default navbar-static-top ">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/">
                        <img src="{{ asset(s('logo')) }}" alt="Smm Panel" title="">
                      </a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li  class="active" >
              <a href="/auth/login" >
                                Sign in
              </a>
            </li>
                        <li >
              <a href="/services" >
                                Services
              </a>
            </li>
                        <li >
              <a href="/apidoc" >
                                API
              </a>
            </li>
                        <li >
              <a href="/auth/register" >
                                Sign up
              </a>
            </li>
                   
                      </ul>
        </div>
      </div>
    </nav>
    <!-- Main variables *content* -->
    <style>
  .guest .navbar-default {
    position: absolute;
    margin: auto;
  }
</style>

<section id="topsec">
  <div class="container">
    <div class="row">
      <div class="col-sm-7">
        <div class="suptitle"><b><span class="blue-txt">SMM Panel No 1</span></b></div>
        <h1 class="main-title">
          SMM Panel<span class="yellow-txt"> -The Best Smm Panel</span> <span class="blue-txt">For your Real Growth.</span> 
        </h1>
        <p class="colored-txt">
          Wow Your Social Media Presence with US!
        </p>
        <p class="txt">
          Welcome to <b>{{ ucfirst(request()->getHost()) }}</b> - Your trusted <b>SMM Panel</b> for cheap, fast and high quality <b>SMM Services</b>. Whether you are an influencer, brand or agency, we offer the most trusted SMM sources for real followers, non-drop social media followers and real-looking profile with real and active engagement. Supported by safe and trusted methods, Our SMM Panel services guarantee stable and long-term follower growth across all social media platforms. Choose the <b>cheapest SMM Panel</b> in the market and grow your social media with ease. 
        </p>
        <div class="btn-wrap">
          <a class="btn btn-secondary" href="/auth/login">Sign up</a>
        </div>
      </div>
      <div class="col-sm-5">
        <div class="top-sec-img-wrap">
          <img class="img-responsive" src="https://i.imgur.com/0673r4n.png" alt="" title="">
        </div>
      </div>
    </div>
  </div>
</section>

</section>
<section id="social">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <div class="social-ul-slider">
          <div>
          <div class="social-box">
              <img class="img-responsive" src="https://i.imgur.com/LRnAzZl.png" alt="Smm Panel" title="Smm Panel">
            </div>
          </div>
          <div>
          <div class="social-box">
              <img class="img-responsive" src="https://i.imgur.com/IfackC7.png" alt="Smm Panel" title="Smm Panel">
            </div>
          </div>
          <div>
          	 <div class="social-box">
              <img class="img-responsive" src="https://i.imgur.com/F9ZAalL.png" alt="Smm Panel" title="Smm Panel">
            </div>
          </div>
          <div>	
          	<div class="social-box">
              <img class="img-responsive" src="https://i.imgur.com/wpTEBZw.png" alt="Smm Panel" title="Smm Panel">
            </div>
          </div>
          <div>
          	<div class="social-box">
              <img class="img-responsive" src="https://i.imgur.com/x0yW7jX.png" alt="Smm Panel" title="Smm Panel">
            </div>
          </div>
          <div>
          <div class="social-box">
              <img class="img-responsive" src="https://i.imgur.com/y7HG0PV.png" alt="Smm Panel" title="Smm Panel">
            </div>
          </div>
          <div>
          	<div class="social-box">
              <img class="img-responsive" src="https://i.imgur.com/OIj5YwL.png" alt="Smm Panel" title="Smm Panel">
            </div>
          </div>
        </div>
        
                
      </div>
    </div>
  </div>
</section>
<section id="why">
  <div class="container">
    <div class="row why-row">
      <div class="col-sm-12">
        <h2 class="title">Why Choose Our SMM Panel?</h2>
        <div class="why-txt-box">
          <p class="txt">In the modern Digital age, social media growth is no longer a trend-it’s a necessity for businesses, influencers, and personal brands. At {{ ucfirst(request()->getHost()) }}, we have the best and cheapest SMM Panel services that can make you grow faster, smarter and more efficiently.
</p>
          <p class="txt">Our <b>Social media panel</b> offers guaranteed access to non-drop social media followers, real and active engagement, all provided through safe and secure means. Our Most trusted and Cheap SMM Panel services are delivered to promote your brand, increase visibility and help save your time and energy. Our Reputation as a top-notch SMM Panel Provider is built on transparency, client satisfaction and performance. Using our Cheap SMM Panel, you can get the following advantages:</p>
          <span id="text">
          <ul>
            <li><p class="txt">Our SMM Panel promotes brand, business, or personal profiles on social media platforms with our premium Services.</p></li>
            <li><p class="txt">By using our <b>social media panel</b> you can increase followers, likes, and views, and makes your social media profile appear more attractive.</p></li>
            <li><p class="txt">With the Use SMM Panel, you can analyze your social media strategy more effectively.</p></li>
          </ul>
<p class="txt">Our easy-to-use dashboard, lightning-fast delivery and customer support sets us apart from our competitors. At <b><a href="https://{{ ucfirst(request()->getHost()) }}/">{{ ucfirst(request()->getHost()) }}</a></b>, you can buy any kind of SMM services to scale your campaign with ease. We’ve helped millions of clients reach their goals through stable and long-term growth and a variety of social media marketing services. If you need a trusted, safe and performance driven <b>SMM Provider</b>, look no further, let {{ ucfirst(request()->getHost()) }} be your solution for social media success. If you need a trusted, safe and performance driven SMM Provider, look no further, let {{ ucfirst(request()->getHost()) }} be your solution for social media success.
</p>
 <p class="txt">Here’s why millions of customers trust our SMM Panel services:</p>
          </span>
          <button id="read-btn">Read More</button>
          <a href="/signup" class="btn btn-primary">Get Started</a>
        </div>
        <div class="why-slider">
          <div>
            <div class="why-slider-wrap">
              <img class="img-responsive why-slider-img" src="https://i.imgur.com/zroXtWW.png" alt="" title="">
              <h3 class="why-slider-title">Fastest SMM Service</h3>
              <p class="why-slider-txt txt">As one of the best smm panels, we provide our customers with the fastest smm services. We process all orders placed on our panel very quickly.</p>
            </div>
          </div>
          <div>
            <div class="why-slider-wrap">
              <img class="img-responsive why-slider-img" src="https://storage.perfectcdn.com/2x0a15/90zb46uwfcgbnyjl.png" alt="" title="">
              <h3 class="why-slider-title">Suitable for all types of Users</h3>
              <p class="why-slider-txt txt">Whether you are a general user, individual creator, agency, a SMM reseller, our cheapest SMM Panel has tailored solutions and SMM services to match your goal.
</p>
            </div>
          </div>
          <div>
            <div class="why-slider-wrap">
              <img class="img-responsive why-slider-img" src="https://storage.perfectcdn.com/2x0a15/9yueylc7tr2qw2hx.png" alt="" title="">
              <h3 class="why-slider-title">Secure and High-Quality Services</h3>
              <p class="why-slider-txt txt">As a trusted SMM Provider, all our SMM services follow safe social media followers acquisition methods, ensuring your account remains protected while getting authentic profiles with real engagement.
</p>
            </div>
          </div>
                    <div>
            <div class="why-slider-wrap">
              <img class="img-responsive why-slider-img" src="https://i.imgur.com/djxjhiA.png" alt="" title="">
              <h3 class="why-slider-title">Cheapest Price</h3>
              <p class="why-slider-txt txt">Our company provides bulk social media likes, views, followers, comments and other SMM Services at the cheapest prices. Our SMM services start at 0.001$.</p>
            </div>
          </div>
          <div>
            <div class="why-slider-wrap">
              <img class="img-responsive why-slider-img" src="https://i.imgur.com/Qbv589Z.png" alt="" title="">
              <h3 class="why-slider-title">24/7 Customer Support</h3>
              <p class="why-slider-txt txt">Having problems in placing or tracking an order on our panel SMM? Our dedicated support team is always active to help you get the most of your campaigns.
</p>
            </div>
          </div>
          <div>
            <div class="why-slider-wrap">
              <img class="img-responsive why-slider-img" src="https://storage.perfectcdn.com/2x0a15/bbjemwiqcrb96rpg.png" alt="" title="">
              <h3 class="why-slider-title">Time Saving and Efficient</h3>
              <p class="why-slider-txt txt">Get instant likes, views, followers and more- no waiting time, no effort required. Our Smmpanel are crafted to boost your social media presence in no time and with ease, so you can focus on what is most important.</p>
            </div>
          </div>
          <div>
            <div class="why-slider-wrap">
              <img class="img-responsive why-slider-img" src="https://storage.perfectcdn.com/2x0a15/d0pzye8g2i5ls4kx.png" alt="" title="">
              <h3 class="why-slider-title">Live Ticket Support</h3>
              <p class="why-slider-txt txt">We have a large support staff that monitors your orders every second and works around the clock to complete them. Our support agents will respond to your ticket within seconds.</p>
            </div>
          </div>
          <div>
            <div class="why-slider-wrap">
              <img class="img-responsive why-slider-img" <img class="img-responsive why-slider-img" src="https://i.imgur.com/djxjhiA.png" alt="" title="">
              <h3 class="why-slider-title">Multiple Payment Option</h3>
              <p class="why-slider-txt txt">Because we are the best SMM panel provider in the world, we serve our individual customers and the owners of child and reseller panels with a variety of payment options, including paypal, paytm, and many more. </p>
            </div>
          </div>
          <div>
            <div class="why-slider-wrap">
              <img class="img-responsive why-slider-img" src="https://storage.perfectcdn.com/2x0a15/cwtyz3wqn0o707ur.png" alt="" title="">
              <h3 class="why-slider-title">API Integration</h3>
              <p class="why-slider-txt txt">For all of our SMM services, we provide our customers with API support so that orders may be placed automatically. This implies that you can incorporate the SMM reseller panel's capabilities into your website for free!</p>
            </div>
          </div>
          <div>
            <div class="why-slider-wrap">
              <img class="img-responsive why-slider-img" src="https://storage.perfectcdn.com/2x0a15/t6lyi0tuy56ad524.png" alt="" title="">
              <h3 class="why-slider-title">Easy-to-use Dashboard</h3>
              <p class="why-slider-txt txt">We offer our users a clean and user-friendly dashboard that provides you with instant access to all our SMM services and important updates in just a few clicks.
</p>
            </div>
          </div>
          <div>
            <div class="why-slider-wrap">
              <img class="img-responsive why-slider-img" src="https://storage.perfectcdn.com/2x0a15/unrp089iug9ttznb.png" alt="" title="">
              <h3 class="why-slider-title">Full Control of an order</h3>
              <p class="why-slider-txt txt">To increase your online visibility as a person or company, we as the cheapest smm panel, provide a wide range of seamlessly connected SMM services. You have complete control over your order because you can place it whenever you want, refill it, or cancel it completely.</p>
            </div>
          </div>
        </div>
        <div class="why-slider-nav"></div>
      </div>
    </div>
  </div>
</section>
<section id="instant">
  <div class="container">
    <div class="row">
      <div class="col-sm-5 edited_col_md">
        <div class="instant-img-wrap">
          <img class="img-responsive" src="https://i.imgur.com/0asIML9.png" alt="Smm Panel" title="Smm Panel">
        </div>
      </div>
      <div class="col-sm-7 edited_col_md">
        <h2 class="title">Get <span class="blue-txt">Instant SMM Services</span> from Our #1 <span class="yellow-txt">Best SMM Panel</span></h2>
        <p class="txt">In today’s busy social media era, it is very difficult to stand out- but with the help of the right resources, it becomes a breeze. At {{ ucfirst(request()->getHost()) }}, we offer the cheapest SMM Panel services that assist increasing your social media audience fast and cheaply. As a Trusted SMM Provider, we offer premium services with fast delivery, and 24/7 Customer support.
</p>
         <p class="txt">Whether you are looking to make the content viral, want more interaction on your posts, and other social media platforms, increase likes, and get more video views, our social media panel provides speedy and dependable results at prices that cannot be beat.</p>
        <div class="row">
          <div class="col-sm-6 edited_col_md">
          	<p class="txt">Our enhancement services through <b>smm panel cheapest</b> are made for influencers, businesses, and marketers to gain an edge over the competition while maintaining total privacy and data security and making the content viral. <span id="text2">With our trusted SMM services for real and active followers, genuine engagement and secure delivery processes, you can build a strong loyal fanbase and enhance your content visibility securely. We make sure your posts get the attention they deserve. If you are a budget constraint but looking for top-notch SMM services, Our Cheap SMM Panel or Cheapest Panel is the right place to achieve your desired outcomes. The Best SMM panel Provider doesn’t only deliver, it assists you grow, engage and convert.</span>
                </p>
            <button id="read-btn2">Read More</button>
            <a href="/signup" class="btn btn-primary">Get Started</a>
          </div>
          <div class="col-sm-6 edited_col_md">
            <div class="instant-stats">
              <div class="instant-stats-left">
                <span class="instant-stats-icon">
                  <i class="fas fa-rocket"></i>
                </span>
              </div>
              <div class="instant-stats-right">
                <p class="instant-stats-txt">An Order Is Made Every</p>
                <h4 class="instant-stats-title">0.14 SEC</h4> 
              </div>
            </div>
            <div class="instant-stats">
              <div class="instant-stats-left">
                <span class="instant-stats-icon">
                  <i class="fas fa-check-double"></i>
                </span>
              </div>
              <div class="instant-stats-right">
                <p class="instant-stats-txt">Orders Completed</p>
                <h4 class="instant-stats-title">4453089</h4> 
              </div>
            </div>
            <div class="instant-stats">
              <div class="instant-stats-left">
                <span class="instant-stats-icon">
                  <i class="far fa-sack-dollar"></i>
                </span>
              </div>
              <div class="instant-stats-right">
                <p class="instant-stats-txt">Price Starting From</p>
                <h4 class="instant-stats-title">$0.01/1K</h4> 
              </div>
            </div>
          </div>
        </div>
        
           </div>
    </div>
  </div>
</section>
<section id="marketing">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <h2 class="title">Affordable and Fast SMM Panels for every Social Media Platform</h2>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <div class="marketing-slider">
          <div>
            <div class="marketing-slider-wrap">
              <h3 class="marketing-slider-title">SMM Panel Instagram</h3>
              <p class="marketing-slider-txt txt"> Looking to boost visibility or increase engagement on Instagram? Our SMM Panel Instagram delivers fast, most efficient SMM services to grow on Instagram organically and economically. Beloved by influencers and businesses alike.</p>
            </div>
          </div>
          <div>
            <div class="marketing-slider-wrap">
              <h3 class="marketing-slider-title">YouTube SMM Panel</h3>
              <p class="marketing-slider-txt txt"> Grow your YouTube Channel fast with real subscribers, views, and likes. Our YouTube SMM Panel provides the tool that assists content creators in boosting content visibility, ranking higher and engaging more audience- all at the best prices.</p>
            </div>
          </div>
          <div>
            <div class="marketing-slider-wrap">
              <h3 class="marketing-slider-title">Indian SMM Panel</h3>
              <p class="marketing-slider-txt txt">{{ ucfirst(request()->getHost()) }} is the most trusted Indian SMM Panel with 7+ years of successful experience. Get Indian specific SMM services for all key platforms at lowest prices to enhance to boost your online presence.</p>
            </div>
            <div>
            </div>
          </div>
          <div>
            <div class="marketing-slider-wrap">
              <h3 class="marketing-slider-title">Instagram Followers Panel</h3>
              <p class="marketing-slider-txt txt">You've come to the perfect place if you want to grow the number of Instagram followers on your account. For your account, our Instagram follower panel provides authentic, high-quality followers. Simply register to use our Instagram Panel Services.</p>
            </div>
          </div>
          <div>
            <div class="marketing-slider-wrap">
              <h3 class="marketing-slider-title"> Twitter SMM Panel</h3>
              <p class="marketing-slider-txt txt">Increase your presence on Twitter through real followers, retweets, and likes. Our Twitter SMM Panel is ideal for businesses, agencies, and creators wishing to grow organically and quickly.</p>
            </div>
          </div>
          <div>
            <div class="marketing-slider-wrap">
              <h3 class="marketing-slider-title">Cheapest SMM Panel India</h3>
              <p class="marketing-slider-txt txt">Still looking for the cheapest smm panel in India. Visit {{ ucfirst(request()->getHost()) }} to find the best and most affordable smm panel service support in India. Your social media growth will be boosted by real followers, likes, and comments from our cheapest smm panel services.</p>
            </div>
          </div>
          <div>
            <div class="marketing-slider-wrap">
              <h3 class="marketing-slider-title">Smm Followers</h3>
              <p class="marketing-slider-txt txt">Use our SMM panel to get real SMM followers and other SMM services if you are new to the business of building a brand on social media and need a sizable number of SMM Followers for your social media account.</p>
            </div>
          </div>
          <div>
            <div class="marketing-slider-wrap">
              <h3 class="marketing-slider-title"> Pakistan SMM Panel</h3>
              <p class="marketing-slider-txt txt">{{ ucfirst(request()->getHost()) }} proudly offers localized and SMM Panel Cheapest services for Pakistan audience, creators, and businesses. Sign up now and enjoy quick delivery, real engagement on content and increase your social media accounts with our Best Pakistan SMM Panel.</p>
            </div>
          </div>
          <div>
            <div class="marketing-slider-wrap">
              <h3 class="marketing-slider-title">SMM Panel USA</h3>
              <p class="marketing-slider-txt txt">Looking for the Best SMM Panel in the USA? Look no further, Our Fastest SMM Panel provides high-quality location specific services optimized for American Users. Boost your reach with high end SMM tools, and real time performance. Register and enjoy our services.</p>
            </div>
          </div>
          <div>
            <div class="marketing-slider-wrap">
              <h3 class="marketing-slider-title">TikTok SMM Panel</h3>
              <p class="marketing-slider-txt txt">Want to become viral on TikTok? Use our TikTok SMM Panel to increase the views and likes on the content through our top-notch Services. Our Packages are cost effective and guaranteed for instant growth.</p>
            </div>
          </div>
          <div>
            <div class="marketing-slider-wrap">
              <h3 class="marketing-slider-title">SMM Reseller Panel</h3>
              <p class="marketing-slider-txt txt">If you want to resell our smm services and make money doing so, you can do so by purchasing our smm reseller panel. Our smm reseller panel is delivered to the majority of our customers, making us one of the best smm reseller panel providers in the globe.
</p>
            </div>
          </div>
          <div>
            <div class="marketing-slider-wrap">
              <h3 class="marketing-slider-title">SMM Panel India</h3>
              <p class="marketing-slider-txt txt">Our company provides high-quality smm services to all of our clients, which makes SMM Panel a well-known smm panel in india. Our company is a leading provider of smm services in India, such as smm followers and smm likes. Customer support is always available to answer questions about orders and purchases.
</p>
            </div>
          </div>
          <div>
            <div class="marketing-slider-wrap">
              <h3 class="marketing-slider-title">Facebook SMM Panel</h3>
              <p class="marketing-slider-txt txt">From Post and Video engagement to page likes, our Facebook SMM Panel assists you in extending your audience and gaining credibility. Grow your brand or individual account with our authentic Facebook marketing solutions.
</p>
            </div>
          </div>
          <div>
            <div class="marketing-slider-wrap">
              <h3 class="marketing-slider-title">Indian SMM Reseller Panel</h3>
              <p class="marketing-slider-txt txt">Our SMM Panel is the best indian smm reseller panel from 2010. With our SMM reseller panel, resellers can resell services after setting their own commissions. We offer the best SMM reseller panel services to both panel owners and individual members of our panel.</p>
            </div>
          </div>
          <div>
            <div class="marketing-slider-wrap">
              <h3 class="marketing-slider-title">Cheapsmmpanel</h3>
              <p class="marketing-slider-txt txt">In the cheap smm market, SMM Panel is well known as a cheap smm panel because we offer a wide range of smm services for a very low price. We accept a wide range of payment options for our cheap smm services, including Paytm, Paypal, Credit Card, Bitcoin, Direct Bank Transfer, etc.</p>
            </div>
          </div>
          <div>
            <div class="marketing-slider-wrap">
              <h3 class="marketing-slider-title">Bangladesh SMM Panel</h3>
              <p class="marketing-slider-txt txt">Experienced the unmatched advantages of the Bangladesh SMM Panel. Our social media panel for Bangladesh offers diverse social media marketing services covering everything from Instagram to TikTok.
</p>
            </div>
          </div>
          <div>
            <div class="marketing-slider-wrap">
              <h3 class="marketing-slider-title">Best SMM Panel</h3>
              <p class="marketing-slider-txt txt">We offer the best SMM promotions on our smart smm panel. Our Best SMM panel will only deliver high-quality Telegram Followers, Fan Page Likes, Twitter Followers, Tiktok Likes, and Followers. Our followers will be consistent, and you will want to place another order with us.</p>
            </div>
          </div>
        </div>
        <div class="marketing-slider-nav"></div>
      </div>
    </div>
  </div>
</section>
<section id="bussiness">
  <div class="container">
    <div class="row">
      <div class="col-sm-6">
        <h2 class="title">Increase your social media presence with Best and Cheapest SMM Panel
</h2>
        <div class="bussiness-slider-wrap">
          <div class="bussiness-slider">
            <div>
              <p class="txt">
                In today's busy digital world, establishing a solid social media presence is no longer a choice- It’s crucial for business development. However, most brands are not able to keep pace because of lack of time, money and marketing knowledge. That’s where {{ ucfirst(request()->getHost()) }}, the cheapest SMM Panel comes in. We offer affordable and reliable SMM services intended to assist brands, creators and resellers in growing on social platforms like Instagram, Facebook, YouTube, etc.</p>
            </div>
            <div>
              <p class="txt">
               Our social media panel stands out for its unbeatable prices and top-notch services. We provide the most trusted SMM services that give real results in no time. We at {{ ucfirst(request()->getHost()) }} specialize in providing 100% genuine, fast and secure social media marketing services for increasing your brand’s presence. Now reaching millions of people is easy with our wide range of services. Whether you want to target a specific demographic or go global, Our Cheap SMM Panel is here to assist. Become one of millions of happy users and resellers who rely on us as their trusted social media marketing panel worldwide.</p>
            </div> 
          </div>
          <div class="bussiness-slider-nav"></div>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="bussiness-img-wrap">
          <img class="img-responsive" src="https://storage.perfectcdn.com/2x0a15/y0f0e4p2uqd072yz.png" alt="" title="Smm Panel">
        </div>
      </div>
    </div>
  </div>
</section>
<section id="work">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <h2 class="title text-center">How our SMM Panel Work?</h2>
        <p class="txt text-center">To assist you enhance the exposure of your brand or business on social media sites, SMM Panel is a social media marketing tool that provides a range of social media marketing services. You can also use the interaction metrics created by the SMM panel with your intended audience to grow your follower and following base. So let's start by examining how our smm panel functions:</p>
      </div>
    </div>
    <div class="row steps-row">
      <div class="col-sm-3">
        <div class="steps-wrap step1">
          <div class="steps-head">
            <span class="steps-num">1</span>
            <span class="steps-img-wrap">
            <img class="img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/r2.png" alt="Smm Panel" title="Smm Panel">
            </span>
          </div>
          <div class="steps-body">
            <h3 class="steps-title">Create an Account</h3>
            <p class="steps-txt txt">Create an account by filling out the sign-up form with your email address, a username, and a password to our smm panel. Then get prepared to become viral.</p>
          </div>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="steps-wrap step2">
          <div class="steps-head">
            <span class="steps-num">2</span>
            <span class="steps-img-wrap">
            <img class="img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/y2.png" alt="Smm Panel" title="Smm Panel">
              </span>
          </div>
          <div class="steps-body">
            <h3 class="steps-title">Add Funds</h3>
            <p class="steps-txt txt">Then, add money to your SMM Panel 
              Wallet (also known as your social 
              media marketing panel wallet) and 
              get ready to soar to stardom. To make 
              donating money easier, we have a 
              variety of payment alternatives 
              included.
            </p>
          </div>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="steps-wrap step3">
          <div class="steps-head">
            <span class="steps-num">3</span>
            <span class="steps-img-wrap">
            <img class="img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/g2.png" alt="Smm Panel" title="Smm Panel">
            </span>
          </div>
          <div class="steps-body">
            <h3 class="steps-title">Place Order/ Service Selection</h3>
            <p class="steps-txt txt">Choose the SMM service package that best fits your goals and financial restrictions, then be ready to increase your social media visibility and exposure.</p>
          </div>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="steps-wrap step4">
          <div class="steps-head">
            <span class="steps-num">4</span>
            <span class="steps-img-wrap">
            <img class="img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/b2.png" alt="Smm Panel" title="Smm Panel">
            </span>
          </div>
          <div class="steps-body">
            <h3 class="steps-title">Boost your Online Presence and Enjoy</h3>
            <p class="steps-txt txt">Currently, chill and observe your social media growth. Our SMM services greatly raise your brand's visibility among your target market across all social media platforms.
            </p>
          </div>
        </div>
      </div>
    </div>
      </div>
</section>
<section id="best">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <h2 class="title text-center">Our Array of Best Selling <br>cheapest SMM Panel Services</h2>
        <ul class="nav nav-tabs best-nav-tabs" id="myTab" role="tablist" >
          <li class="nav-item active">
            <a class="nav-link " id="home-tab" data-toggle="tab" href="#facebook" role="tab" aria-controls="home" aria-selected="true">
              <img class="img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/fb1.png" alt="Smm Panel" title="Smm Panel">
              Facebook</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#twitter" role="tab" aria-controls="profile" aria-selected="false">
              <img class="img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/twitter1.png" alt="Smm Panel" title="Smm Panel">
              Twitter</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="contact-tab" data-toggle="tab" href="#instagram" role="tab" aria-controls="contact" aria-selected="false">
              <img class="img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/inst1.png" alt="Smm Panel" title="Smm Panel">
              Instagram</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="contact-tab" data-toggle="tab" href="#youtube" role="tab" aria-controls="contact" aria-selected="false">
              <img class="img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/yt1.png" alt="Smm Panel" title="Smm Panel">
              Youtube</a>
          </li>
                   <li class="nav-item">
            <a class="nav-link" id="contact-tab" data-toggle="tab" href="#tiktok" role="tab" aria-controls="contact" aria-selected="false">
              <img class="img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/tiiktok1.png" alt="Smm Panel" title="Smm Panel">
              Tiktok</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="contact-tab" data-toggle="tab" href="#linkedin" role="tab" aria-controls="contact" aria-selected="false">
              <img class="img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/linkedin1.png" alt="Smm Panel" title="Smm Panel">
              Linkedin</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="contact-tab" data-toggle="tab" href="#telegram" role="tab" aria-controls="contact" aria-selected="false">
              <img class="img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/telegram1.png" alt="Smm Panel" title="Smm Panel">
              Telegram</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="contact-tab" data-toggle="tab" href="#pinterest" role="tab" aria-controls="contact" aria-selected="false">
              <img class="img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/pintrest1.png" alt="Smm Panel" title="Smm Panel">
              Pinterest</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="contact-tab" data-toggle="tab" href="#spotify" role="tab" aria-controls="contact" aria-selected="false">
              <img class="img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/spotify1.png" alt="Smm Panel" title="Smm Panel">
              Spotify</a>
          </li>
        </ul>
        <div class="best-nav-tabs-nav"></div>
        <div class="tab-content" id="myTabContent">
          <div class="tab-pane fade in active" id="facebook" role="tabpanel" aria-labelledby="home-tab">
            <div class="row">
              <div class="col-sm-6">
                <div class="best-tab-wrap">
                  <h3 class="best-tab-title">Facebook SMM Panel Service</h3>
                  <p class="txt">Facebook is the most commonly used social media platform because it provides more sharing and commenting options than any other social network and serves as a great promotional tool for content as well. Today's social media marketing has been affected by Facebook, a big social media network. About 80-90% of marketers upload content on Facebook and advertise their brands there.
</p>
                  <p class="txt">So, use our Facebook SMM Panel Services to improve brand recognition on Facebook. Some of our most popular Facebook smm panel services include:
</p>
                  <ul>
                    <li>
                      <p><i class="far fa-thumbs-up"></i> Facebook Page Likes</p>
                    </li>
                    <li>
                      <p><i class="far fa-thumbs-up"></i> Facebook Post Likes</p>
                    </li>
                    <li>
                      <p><i class="fal fa-bullseye-arrow"></i> Facebook post shares</p>
                    </li>
                    <li>
                      <p><i class="far fa-thumbs-up"></i> Facebook custom comments </p>
                    </li>
                    <li>
                      <p><i class="fas fa-eye"></i> Facebook Page Followers</p>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="best-tab-img">
                  <img class="img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/fb-tab.png" alt="Smm Panel">
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="twitter" role="tabpanel" aria-labelledby="profile-tab">
            <div class="row">
              <div class="col-sm-6">
                <div class="best-tab-wrap">
                  <h3 class="best-tab-title">Twitter SMM Panel </h3>
                  <p class="txt">Social networking site Twitter offers the opportunity to retweet most information and runs continually. By enhancing online markets using SMM, Twitter increases the growth of a firm. We provide a Twitter SMM Panel that enables you to grow your Twitter company profile's followers, tweets, likes, and other metrics. Twitter's SMM panel services include the following:
</p>
                  <ul>
                    <li>
                      <p><i class="fas fa-tint-slash"></i> Twitter followers</p>
                    </li>
                    <li>
                      <p><i class="fas fa-users"></i> Twitter Tweets</p>
                    </li>
                    <li>
                      <p><i class="fas fa-tag"></i> Twitter retweets </p>
                    </li>
                    <li>
                      <p><i class="far fa-thumbs-up"></i> Twitter comments</p>
                    </li>
                    <li>
                      <p><i class="fas fa-tag"></i> Twitter Share</p>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="best-tab-img">
                  <img class="img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/twitter-tab.png" alt="Smm Panel">
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="instagram" role="tabpanel" aria-labelledby="contact-tab">
            <div class="row">
              <div class="col-sm-6">
                <div class="best-tab-wrap">
                  <h3 class="best-tab-title">Instagram SMM Panel</h3>
                  <p class="txt">Using Instagram for business can increase brand awareness, increase sales, and track audience engagement. One of the best ways to find customers is to go to where they already hang out. We can assist your business in growing its Instagram following in order to attract more customers. With our Instagram services, you'll receive Non-Drop High-Quality Accounts, basic followers, video views, story views, and followers from Turkey, India, the United States, and other countries. Using Instagram, you can write custom comments, see impressions, saves, vote on stories, and click a user's profile to see what they're up to. The following Instagram SMM panel services are provided by us:
</p>
                  <ul>
                    <li>
                      <p><i class="fas fa-users"></i> Buy Instagram likes</p>
                    </li>
                    <li>
                      <p><i class="far fa-thumbs-up"></i> Buy Instagram views here</p>
                    </li>
                    <li>
                      <p><i class="fas fa-eye"></i> Buy Instagram followers</p>
                    </li>
                    <li>
                      <p><i class="fal fa-comments"></i> Buy Instagram comments</p>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="best-tab-img">
                  <img class="img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/insta-tab.png" alt="Smm Panel">
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="youtube" role="tabpanel" aria-labelledby="contact-tab">
            <div class="row">
              <div class="col-sm-6">
                <div class="best-tab-wrap">
                  <h3 class="best-tab-title">Youtube SMM Panel</h3>
                  <p class="txt">Businesses may interact effectively with their current and potential clients by using YouTube videos to effectively market themselves. Developing a YouTube brand or company does not require much more than a large subscriber base. By using our Youtube SMM Panel, you will be able to build a strong subscriber base on Youtube. In addition, our YouTube SMM panel will also supply you with non-dropping, natural views for your YouTube videos. With the help of our system, your views will increase automatically, and we'll share your videos on other platforms so you can start receiving some organic views in addition to the ones you buy. Services that we provide as part of our Youtube SMM Panel include:</p>
                  <ul>
                    <li>
                      <p><i class="fas fa-tint-slash"></i> You can buy Youtube subscribers online
</p>
                    </li>
                    <li>
                      <p><i class="fal fa-bullseye-arrow"></i> You can buy Youtube subscribers and likes online</p>
                    </li>
                    <li>
                      <p><i class="fas fa-american-sign-language-interpreting"></i> Views for Youtube Subscribers Buy Online</p>
                    </li>
                    <li>
                      <p><i class="fal fa-comments"></i> You can buy Youtube shares online</p>
                    </li>
                    <li>
                      <p><i class="fas fa-hourglass-end"></i> You can buy Youtube subscribers comments online</p>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="best-tab-img">
                  <img class="img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/yt-tab.png" alt="Smm Panel">
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="snapchat" role="tabpanel" aria-labelledby="contact-tab">
            <div class="row">
              <div class="col-sm-6">
                <div class="best-tab-wrap">
                  <h3 class="best-tab-title">SnapChat SMM Panel</h3>
                  <p class="txt">Businesses may interact effectively with their current and potential clients by using YouTube videos to effectively market themselves. Developing a YouTube brand or company does not require much more than a large subscriber base. By using our Youtube SMM Panel, you will be able to build a strong subscriber base on Youtube. In addition, our YouTube SMM panel will also supply you with non-dropping, natural views for your YouTube videos. With the help of our system, your views will increase automatically, and we'll share your videos on other platforms so you can start receiving some organic views in addition to the ones you buy. Services that we provide as part of our Youtube SMM Panel include:</p>
                  <ul>
                    <li>
                      <p><i class="fas fa-tint-slash"></i> You can buy Youtube subscribers online
</p>
                    </li>
                    <li>
                      <p><i class="fal fa-bullseye-arrow"></i> You can buy Youtube subscribers and likes online</p>
                    </li>
                    <li>
                      <p><i class="fas fa-american-sign-language-interpreting"></i> Views for Youtube Subscribers Buy Online</p>
                    </li>
                    <li>
                      <p><i class="fal fa-comments"></i> You can buy Youtube shares online</p>
                    </li>
                    <li>
                      <p><i class="fas fa-hourglass-end"></i> You can buy Youtube subscribers comments online</p>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="best-tab-img">
                  <img class="img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/snapchat-tab.png" alt="Smm Panel">
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="tiktok" role="tabpanel" aria-labelledby="contact-tab">
            <div class="row">
              <div class="col-sm-6">
                <div class="best-tab-wrap">
                  <h3 class="best-tab-title">Tiktok SMM Panel</h3>
                  <p class="txt">Businesses may interact effectively with their current and potential clients by using YouTube videos to effectively market themselves. Developing a YouTube brand or company does not require much more than a large subscriber base. By using our Youtube SMM Panel, you will be able to build a strong subscriber base on Youtube. In addition, our YouTube SMM panel will also supply you with non-dropping, natural views for your YouTube videos. With the help of our system, your views will increase automatically, and we'll share your videos on other platforms so you can start receiving some organic views in addition to the ones you buy. Services that we provide as part of our Youtube SMM Panel include:</p>
                  <ul>
                    <li>
                      <p><i class="fas fa-tint-slash"></i> You can buy Youtube subscribers online
</p>
                    </li>
                    <li>
                      <p><i class="fal fa-bullseye-arrow"></i> You can buy Youtube subscribers and likes online</p>
                    </li>
                    <li>
                      <p><i class="fas fa-american-sign-language-interpreting"></i> Views for Youtube Subscribers Buy Online</p>
                    </li>
                    <li>
                      <p><i class="fal fa-comments"></i> You can buy Youtube shares online</p>
                    </li>
                    <li>
                      <p><i class="fas fa-hourglass-end"></i> You can buy Youtube subscribers comments online</p>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="best-tab-img">
                  <img class="img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/tiktok-tab.png" alt="Smm Panel">
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="linkedin" role="tabpanel" aria-labelledby="contact-tab">
            <div class="row">
              <div class="col-sm-6">
                <div class="best-tab-wrap">
                  <h3 class="best-tab-title">LinkedIn SMM Panel</h3>
                  <p class="txt">LinkedIn is a fantastic platform for businesses and professionals to connect with one another in order to generate leads, increase brand recognition, and engage in lead generation activities. By effectively utilizing this social network, you may use it as an active marketing channel to grow your network and business. Some of the crucial indicators for brands or businesses on LinkedIn are connections, impressions, reach, profile views, and post views. Therefore, you can use our LinkedIn SMM Panel if you're seeking for a quicker solution to boost these KPIs. We are the cheapest SMM reseller Panel where you can get engagement, post views, and connections on LinkedIn for less money.</p>
                  <p class="txt">Here are some common LinkedIn services that you can get through our SMM Panel:</p>
                  <ul>
                    <li>
                      <p><i class="fas fa-tint-slash"></i> LinkedIn Active Connections</p>
                    </li>
                    <li>
                      <p><i class="fal fa-bullseye-arrow"></i> LinkedIn Post Comments</p>
                    </li>
                    <li>
                      <p><i class="fas fa-american-sign-language-interpreting"></i> LinkedIn Post views</p>
                    </li>
                    <li>
                      <p><i class="fal fa-comments"></i> LinkedIn Post Likes and Shares</p>
                    </li>
                    <li>
                      <p><i class="fas fa-hourglass-end"></i> LinkedIn Followers</p>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="best-tab-img">
                  <img class="img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/linkedin-tab.png" alt="Smm Panel">
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="telegram" role="tabpanel" aria-labelledby="contact-tab">
            <div class="row">
              <div class="col-sm-6">
                <div class="best-tab-wrap">
                  <h3 class="best-tab-title">Telegram SMM Panel</h3>
                  <p class="txt">Telegram is a platform for instant messaging, so you may send and receive messages to and from your friends. Businesses use Telegram to interact directly with customers. Businesses can set up groups on Telegram where they can inform their clients about their brands on a daily basis. Businesses have been utilizing Telegram since it is a platform with a high level of interaction and you may have as many users as you want because there are no membership restrictions. If you are new in business and want active members for your telegram group then you can use our Telegram SMM Panel. We are the cheapest panel from where you can buy Telegram Subscribers, votes, Members etc. 	</p>
                  <p class="txt">Use our Telegram SMM Panel Services to increase your brand's visibility on Telegram as a result. The following are some of our most popular Telegram SMM panel services:</p>
                  <ul>
                    <li>
                      <p><i class="fas fa-tint-slash"></i> Telegram Members</p>
                    </li>
                    <li>
                      <p><i class="fal fa-bullseye-arrow"></i> Telegram Channel Subscribers</p>
                    </li>
                    <li>
                      <p><i class="fas fa-american-sign-language-interpreting"></i> Telegram Post Views</p>
                    </li>
                    <li>
                      <p><i class="fal fa-comments"></i> Telegram Post Reactions</p>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="best-tab-img">
                  <img class="img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/telegram-tab.png" alt="Smm Panel">
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="pinterest" role="tabpanel" aria-labelledby="contact-tab">
            <div class="row">
              <div class="col-sm-6">
                <div class="best-tab-wrap">
                  <h3 class="best-tab-title">Pinterest SMM Panel</h3>
                  <p class="txt"> Pinterest is a visual social media site that enables users to create and share pins, as well as build boards and shop. Two benefits of using Pinterest for business are a broader pool of potential clients and brand visibility. Through a variety of videos and images, Pinterest may both help you make money and give you fun. This is the ideal platform for driving massive traffic to your website and other social media accounts. 
</p>
                  <p class="txt">Therefore, you can use our SMM Panel to increase the visibility of your business on Pinterest and expand the audience of both your normal and business Pinterest accounts. Some of the common Pinterest services that you can buy through our SMM Panel are:</p>
                  <ul>
                    <li>
                      <p><i class="fas fa-tint-slash"></i> Pinterest followers</p>
                    </li>
                    <li>
                      <p><i class="fal fa-bullseye-arrow"></i> Pinterest Pins</p>
                    </li>
                    <li>
                      <p><i class="fas fa-american-sign-language-interpreting"></i> Pinterest Auto Shares</p>
                    </li>
                    <li>
                      <p><i class="fal fa-comments"></i> Website Traffic from Pinterest</p>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="best-tab-img">
                  <img class="img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/pintrest-tab.png" alt="Smm Panel">
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="spotify" role="tabpanel" aria-labelledby="contact-tab">
            <div class="row">
              <div class="col-sm-6">
                <div class="best-tab-wrap">
                  <h3 class="best-tab-title">Spotify SMM Panel</h3>
                  <p class="txt">Are you looking for the best and cheapest method to purchase Spotify Plays? Welcome to the SMM Panel, then. We are the top provider of Real & Affordable Spotify Plays, music promotion, and all other digital streaming platforms. We offer premium, organic Spotify streaming services with quick, secure delivery, no drops, and affordable pricing.</p>
                  <p class="txt">Whether you want to purchase Spotify plays, increase your follower count, obtain more playlist plays, promote your song, or simply break into the music business, {{ ucfirst(request()->getHost()) }}l is here to help.</p>
                  <p class="txt">Here are some common Spotify services that you can get through our SMM Panel:</p>
                  <ul>
                    <li>
                      <p><i class="fas fa-tint-slash"></i> Spotify Saves</p>
                    </li>
                    <li>
                      <p><i class="fal fa-bullseye-arrow"></i> Spotify Album Plays</p>
                    </li>
                    <li>
                      <p><i class="fas fa-american-sign-language-interpreting"></i> Spotify Premium Plays</p>
                    </li>
                    <li>
                      <p><i class="fal fa-comments"></i> Spotify User Followers</p>
                    </li>
                    <li>
                      <p><i class="fal fa-comments"></i> Spotify Playlist Followers</p>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="best-tab-img">
                  <img class="img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/spotify-tab.png" alt="Smm Panel">
                </div>
              </div>
            </div>
          </div>
          
        </div>
      </div>
    </div>
  </div>
</section>
<section id="clients">
  <div class="container">
    <div class="row">
      <div class="col-sm-6">
        <h2 class="title">Our Client’s Testimonials</h2>
        <div class="testi-slider">
          <div>
            <div class="testi-slide">
              <h3 class="testi-title">
                <span class="testi-name">Samiksha Sharma</span>
                <span class="testi-name-quote"><i class="fas fa-quote-right"></i></span>
              </h3>
              <p class="testi-txt">I had lost all enthusiasm for managing my social media accounts until I came across SMM Panel. Their bundles are fantastic and they only offer high-quality services. Their live support team is fantastic as well. For me, this is the best SMM panel.
</p>
              <img class="img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/stars.png" alt="Smm Panel" title="Smm Panel">
            </div>
          </div>
          <div>
            <div class="testi-slide">
              <h3 class="testi-title">
                <span class="testi-name">Michael Jackson</span>
                <span class="testi-name-quote"><i class="fas fa-quote-right"></i></span>
              </h3>
              <p class="testi-txt">It took us a long time to find Smm Panel after searching for the best SMM panel for a long time. We purchased 2000 Facebook likes for our company, and it worked! Additionally, we offer 24 hour customer service seven days a week. In my opinion, SMM Panel is the best reseller.
</p>
              <img class="img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/stars.png" alt="Smm Panel" title="Smm Panel">
            </div>
          </div>
          <div>
            <div class="testi-slide">
              <h3 class="testi-title">
                <span class="testi-name">Alex Hales</span>
                <span class="testi-name-quote"><i class="fas fa-quote-right"></i></span>
              </h3>
              <p class="testi-txt">It was very easy to use the services, and I received my Instagram likes and Facebook page likes instantly. It has been a long time since I have bought from them, and I look forward to buying more in the future. Definitely the cheapest SMM panel available.</p>
              <img class="img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/stars.png" alt="Smm Panel" title="Smm Panel">
            </div>
          </div>
          <div>
            <div class="testi-slide">
              <h3 class="testi-title">
                <span class="testi-name">Ankita Verma</span>
                <span class="testi-name-quote"><i class="fas fa-quote-right"></i></span>
              </h3>
              <p class="testi-txt">Hello, this is one of the best SMM panels available. I bought Instagram Likes from that panel. I received likes for my post within an hour of placing my order. It is strongly recommended that everyone should use this SMM panel.
</p>
              <img class="img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/stars.png" alt="Smm Panel" title="Smm Panel">
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="client-img-wrap">
        	<img class="img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/clients-img.png" alt="Smm Panel" title="Smm Panel">
        </div>
      </div>
    </div>
  </div>
</section>
<section id="faq">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <h2 class="title text-center">Frequently Asked Question</h2>
        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
          <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingOne">
              <h3 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne" class="collapsed">What is the use of an SMM panel?
                </a>
              </h3>
            </div>
            <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne" aria-expanded="false" style="height: 0px;">
              <div class="panel-body">
               An SMM panel or social media marketing panel is a powerful web tool that can automate and boost your social media marketing efforts in just a few clicks. Companies in today’s world are dependent on a strong online presence to bring customers, establish brand trustworthiness, and expand quicker. However, most of the brands, creators don’t have the time and budget to continually handle social media expansion. That is where an SMM Panel is useful. 
<p>
  SMM Panel is a tool which is useful in boosting social media presence and engagement. With an SMM Panel like {{ ucfirst(request()->getHost()) }} , you have the access to a centralized dashboard providing instant SMM services like- followers, video views, likes, comments, shares, and saves, etc for all major social media platforms like- Instagram, YouTube, Twitter, TikTok, etc at reasonable prices.
                </p>
                <p><b>Benefits of using an SMM Panel</b>
                <br>
<br>There are countless benefits and advantages of using a Trusted SMM Panel. Some of them are as follows:
                  <br>
<br>1. Compared to paid ads or influencer promotions, SMM panels are very cheap. You can expand your campaigns without exhausting your budget.
<br>2. Forget about spending hours and hours developing your profile and increasing the engagement manually. With a few clicks, you can order SMM services and experience quick results.
<br>3. SMM Panel improves Brand Authority on social media platforms. A solid presence on social media with a good engagement rate builds trust among users, making your brand look more professional and reliable.
<br>4. SMM Panel are best and ideal for users who want to generate money online as they can begin reselling our social media marketing services. We serve as a trusted SMM reseller Panel. Resellers can easily handle multiple clients thanks to white-label features and APIs.
<br>5. SMM panels such as {{ ucfirst(request()->getHost()) }} provide a simple dashboard in which you can place orders, monitor orders, campaigns, and access various SMM services on one screen.
<br>6. 	With International and country specific Social media marketing services, at your fingertips, you can expand your audience anywhere globally.
<br>7. SMM Panels such as {{ ucfirst(request()->getHost()) }} have an assurance that your data and campaign information are not publicized. Safe Payment systems and encrypted systems provide an additional layer of security.
</p>
                <p>In short, an SMM Panel makes your social media marketing easy, enabling you to get noticed, drive engagement on content, and increase your brand awareness more quickly. Whether you are a single creator, or a company or a general user looking for fast online development, an SMM Panel is an efficient choice to boost your online presence.
</p>
              </div>
            </div>
          </div>
          <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingTwo">
              <h3 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                  Which SMM panel is the best?
                </a>
              </h3>
            </div>
            <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo" aria-expanded="false">
              <div class="panel-body">
               Discovering and Selecting the Best SMM Panel is totally based on your objectives, budget, and features you are looking for. If you are a creator, digital agency, social media manager, or reseller, Selecting the Correct and Best SMM panel can greatly affect your growth.
                <br>
                <p><b>What makes an SMM Panel the Best?</b></p>
                <br>
                Here’s is what features you should consider when looking for the Best SMM Panel:
                 <br>
                <br><b>1. Broader Range of Services</b>- High quality social media panel provides all SMM services for Instagram, TikTok, twitter, YouTube, and others- including likes, followers, video views, and other engagement metrics.
                <br><b>2. Instant Order Processing</b>- Speed is important. The Best SMM Panels provide instant or rapid delivery, real-time tracking, and precious order updates.
                <br><b>3. Safe and Flexible Payment Methods</b>- A trusted and secure SMM Panel should have multiple payment options such as Credit/Debit Cards PayPal, Paytm, UPI Cryptocurrencies (USDT, BTC, BUSD, etc.) Wallet systems and local methods (JazzCash, EasyPaisa, Gcash, etc.).
                <br><b>4. Simple to Use Dashboard</b>- A tidy, simple dashboard assists in placing and managing orders with ease. A mobile friendly interface is the cherry on top.
 <br><b>5. 24/7 Customer Support</b>- 24/7 Customer Support through live chat, email, or ticket system guarantees a seamless experience even when problems arise while placing or tracking the order.
<br>
<br>
                <p>At {{ ucfirst(request()->getHost()) }}, we are committed to becoming your first choice by providing a combination of Quality, speed, support and security that no other company can match.
We bring together all things that you require in an SMM Panel- lightning fast delivery of SMM services, secure payment methods, and personal customer support. Ready to grow your social media presence the smart way?
Sign Up Now and begin growing your business with the Top SMM Panel in the market.
</p>

              </div>
            </div>
          </div>
          <div class="panel panel-default" >
            <div class="panel-heading" role="tab" id="headingThree">
              <h3 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                  What is the SMM Reseller Panel?
                </a>
              </h3>
            </div>
            <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree" aria-expanded="false">
              <div class="panel-body">
                Reselling SMM services using SMM panels is unquestionably the most well-liked way to run a firm like that. A SMM reseller panel functions like an online store where users can buy SMM services. Both their owners and their clients can easily use them.


              </div>
            </div>
          </div>
          <div class="panel panel-default" >
            <div class="panel-heading" role="tab" id="headingThree">
              <h3 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapsefour" aria-expanded="false" aria-controls="collapseThree">
                  Are SMM panels legal?
                </a>
              </h3>
            </div>
            <div id="collapsefour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree" aria-expanded="false">
              <div class="panel-body">
               The SMM Panel is not illegal. Using an SMM panel to buy and sell social media services is legal. If the SMM panel is not used carefully, it may result in issues for their clients. As a result, you can use the SMM Panel to buy social media marketing (SMM) services without any problems; however, you must do so by hand, not by using a bot.



              </div>
            </div>
          </div>
          <div class="panel panel-default" >
            <div class="panel-heading" role="tab" id="headingThree">
              <h3 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapsefive" aria-expanded="false" aria-controls="collapseThree">
                  What is a SMM panel?
                </a>
              </h3>
            </div>
            <div id="collapsefive" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree" aria-expanded="false">
              <div class="panel-body">
               This is a marketing panel that provides affordable social media marketing and SEO services. This software is very useful for people who want to maximize the potential of their businesses. You can purchase likes, followers, views, subscribers, etc., through the SMM panel. Businesses and companies make use of SMM panels on Instagram, Twitter, and Facebook to advertise their products and services in front of potential customers by increasing the follower base of profile.</div>
            </div>
          </div>
          <div class="panel panel-default" >
            <div class="panel-heading" role="tab" id="headingThree">
              <h3 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapsesix" aria-expanded="false" aria-controls="collapseThree">
                  Is it safe to purchase social media services through the SMM panel?
                </a>
              </h3>
            </div>
            <div id="collapsesix" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree" aria-expanded="false">
              <div class="panel-body">
               Yes, it is safe to purchase social media marketing services through SMM Panel as we provide the best SMM panel services. You can grow your online business very quickly. Also you can get all the services at a very friendly cost.</div>
            </div>
          </div>
          <div class="panel panel-default" >
            <div class="panel-heading" role="tab" id="headingThree">
              <h3 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseseven" aria-expanded="false" aria-controls="collapseThree">
                 What are the best payment methods to use for an SMM Panel?
                </a>
              </h3>
            </div>
            <div id="collapseseven" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree" aria-expanded="false">
              <div class="panel-body">
               When operating or buying SMM services from a Cheap SMM Panel, choosing the appropriate payment method is essential for safe, smooth and trouble free transactions. As individuals, agencies, creators, and resellers from all countries bought SMM services, SMM Panel needs to provide severe flexible and region supported payment options to address various user preferences.

<p>At {{ ucfirst(request()->getHost()) }}, we facilitate a variety of payment options so that users can deposit funds into their accounts with confidence and ease. There are lots of payment options available for the smooth processing of payments.</p>
 
                <p>Most popular payment options accepted by leading SMM Panel providers are: Credit card / Debit card, Crypto payments like USDT / USDC / BUSD through coinpayments, coinbase, and PayTm SMM Panel. Other options include: bank transfers and Wallets such as Jazzcash, EasyPaisa, CashMaal, Gcash, E-wallet such as Payeer, perfect money, Webmoney, PayOp, AdvCash, Enot, etc, Paypal, Cardlink USD, Stripe, Razorpay, etc.</p>
<br>                
                <b>How to pick a suitable Payment Option for SMM Panels?</b>
                <br>
Picking up the right payment method is not just convenient. It involves compliance, cost, trust of the user and balancing ease. Factors that follow are what to pay close attention to:
                <br>
                <br><b>1. Ease of Use-</b> Ensure that paying is easy for users and instant. The transactions may get discarded halfway through if it is difficult to follow.
                <br><b>2. Transaction Fees and Cost effectiveness-</b> Low fee payment gateways ensure maximum profit margin, especially for SMM resellers. Compare Prices and select those payment methods that don't take a bite out of your earnings.
<br><b>3. Geographical Compatibility-</b> Utilize region specific payment gateways to facilitate seamless user experience. (Eg- JazzCash in Pakistan, Paytm in India, GCash in the Philippines)
                <br><b>4. Legal and Compliant-</b> Make sure the payment option is compliant with financial laws in your nation and your users' nations. Stay away from gateways that are in contravention of global payment legislation.
                <br>
                <br>
Having a secure and variety of payment methods is no longer a luxury, now it is a requirement. By providing multiple payment options on SMM panels, you can enroll international users, gain credibility, and grow your SMM Panel effortlessly.
</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>




    <footer>
      <div class="ftr-top">
        <div class="container">
          <div class="row">
            <div class="col-sm-5">
              <div class="ftr-logo-wrap">
                <img class="img-responsive" src="{{ asset(s('logo')) }}" alt="Smm Panel" title="Smm Panel">
              </div>
              <p class="ftr-txt">Your trusted SMM Panel for cheap, fast and high quality SMM Services.Chooss {{ ucfirst(request()->getHost()) }} in the market and grow your social media with ease.
              </p>
              <p class="copyright2"><a href="/">{{ ucfirst(request()->getHost()) }}</a> © 2022. All rights reserved. </p>

              <ul class="social-share">
                <li>
                  <a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a>
                </li>
                <li>
                  <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                </li>
                <li>
                  <a href="#" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                </li>
                <li>
                  <a href="#" target="_blank"><i class="fab fa-pinterest-p"></i></a>
                </li>

              </ul>
            </div>
            <div class="col-sm-2">
              <h2 class="ftr-title">Services</h2>
              <ul class="ftr-nav">
                <li>
                  <a href="/services">Facebook</a>
                </li>
                <li>
                  <a href="/services">Instagram</a>
                </li>
                <li>
                  <a href="/services">Twitter</a>
                </li>
                <li>
                  <a href="/services">Linkedin</a>
                </li>
                <li>
                  <a href="/services">Spotify</a>
                </li>
              </ul>
            </div>
            <div class="col-sm-2">
              <h2 class="ftr-title">Quick Links</h2>
              <ul class="ftr-nav">
                <li>
                  <a href="/">Home</a>
                </li>
                <li>
                  <a href="/services">Services</a>
                </li>
                <li>
                  <a href="/apidoc">Api</a>
                </li>
               
              </ul>
            </div>
            <div class="col-sm-3">
              <h2 class="ftr-title">NewsLetter</h2>
              <div class="newletter-wrap">
                <div class="form-group">
                  <input class="form-control" type="text" placeholder="Enter your email ">
                  <button type="submit" class="btn btn-primary">Subscribe</button>
                </div>
              </div>
              <img class="newsletter-img img-responsive" src="https://yourpanelassets.com/projects/smmpanelcom/img/ftr-img.png" alt="">
            </div>
          </div>
        </div>
      </div>
       
    </footer>
        


    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-11434066917">
</script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'AW-11434066917');
</script>

<script src="https://browser.sentry-cdn.com/9.12.0/bundle.min.js" crossorigin="anonymous" data-lazy="no"></script>

<script>Sentry.init({ dsn: 'https://61b30ccdbd7bc003a750ee837c497280@sentry.io/4504813329711104', defaultIntegrations: false });</script>




<div class="integration-fixed integration-fixed__bottom-right">
    <style>
    .whatsapp-button {
        width: 60px;
        height: 60px;
        bottom: 40px;
        right: 40px;
        background-color: #25d366;
        color: #FFF !important;
        border-radius: 50px;
        text-align: center;
        font-size: 30px;
        box-shadow: 2px 2px 3px #999;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none !important;
        -webkit-transition: all 0.3s ease;
        -moz-transition: all 0.3s ease;
        -o-transition: all 0.3s ease;
        -ms-transition: all 0.3s ease;
        transition: all 0.3s ease;
        transform: scale(0.9)
    }

    .whatsapp-button svg {
        fill: #fff;
    }

    .whatsapp-button:hover {
        transform: scale(1);
        background-color: #1fcc5f;
    }
</style>


        <script type="text/javascript"  src="https://storage.perfectcdn.com/libs/jquery/1.12.4/jquery.min.js" >
          </script>
        <script type="text/javascript"  src="https://storage.perfectcdn.com/global/fgks9m94k0nhqmix.js" >
          </script>
        <script type="text/javascript"  src="https://storage.perfectcdn.com/global/8gmoznjnttfyk1mz.js" >
          </script>
        <script type="text/javascript"  src="https://storage.perfectcdn.com/global/w8l498eitwhkze7w.js" >
          </script>
        <script type="text/javascript"  src="https://storage.perfectcdn.com/global/2k4ql5ke4jrdfp9j.js" >
          </script>
        <script type="text/javascript"  src="https://storage.perfectcdn.com/2x0a15/2k9gjxsmh9ecitau.js" >
          </script>
        <script type="text/javascript"  src="https://storage.perfectcdn.com/2x0a15/amjakdcrkfq0ubyy.js" >
          </script>
        <script type="text/javascript" >
            window.modules.layouts = {"theme_id":1,"auth":0,"live":true,"csrftoken":"b7BBik67slQA1Nxga9JrRB_Czu1awuy-0G9xA1NQkBcqgzSnLYz-I22jgxIn5SNpcayKqSO3p4bkXiRTBGPgcg=="};
          </script>
        <script type="text/javascript" >
            window.modules.signin = [];
          </script>
        <script type="text/javascript" >
          </script>
    




    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script defer src="https://i.23robo.info/projects/{{ ucfirst(request()->getHost()) }}/js/main.js"></script>
    <script defer src="https://i.23robo.info/projects/{{ ucfirst(request()->getHost()) }}/js/howler.js"></script>
    <script defer src="https://i.23robo.info/projects/{{ ucfirst(request()->getHost()) }}/js/player.js"></script>

        <script>
      $(document).ready(function() {
        $("#read-btn").click(function() {
          var elem = $("#read-btn").text();
          if (elem == "Read More") {
            //Stuff to do when btn is in the read more state
            $("#read-btn").text("Read Less");
            $("#text").slideDown();
          } else {
            //Stuff to do when btn is in the read less state
            $("#read-btn").text("Read More");
            $("#text").slideUp();
          }
        });
      });
      $(document).ready(function() {
        $("#read-btn1").click(function() {
          var elem = $("#read-btn1").text();
          if (elem == "Read More") {
            //Stuff to do when btn is in the read more state
            $("#read-btn1").text("Read Less");
            $("#text1").slideDown();
          } else {
            //Stuff to do when btn is in the read less state
            $("#read-btn1").text("Read More");
            $("#text1").slideUp();
          }
        });
      });
      $(document).ready(function() {
        $("#read-btn2").click(function() {
          var elem = $("#read-btn2").text();
          if (elem == "Read More") {
            //Stuff to do when btn is in the read more state
            $("#read-btn2").text("Read Less");
            $("#text2").slideDown();
          } else {
            //Stuff to do when btn is in the read less state
            $("#read-btn2").text("Read More");
            $("#text2").slideUp();
          }
        });
      });
    </script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script>
      $('.why-slider').slick({
        dots: false,
        infinite: true,
        arrows:true,
        speed: 300,
        slidesToShow: 3,
        slidesToScroll: 3,
        appendArrows: $(".why-slider-nav"),
        nextArrow: '<button class="right-arrow"><i class="far fa-angle-right"></i></button>',
        prevArrow: '<button class="left-arrow"><i class="far fa-angle-left"></i></button>',
        responsive: [
          {
            breakpoint: 1024,
            settings: {
              slidesToShow: 3,
              slidesToScroll: 3,
            }
          },
          {
            breakpoint: 992,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 2
            }
          },
          {
            breakpoint: 768,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1
            }
          }
        ]
      });
      $('.best-nav-tabs1').slick({
        dots: false,
        infinite: true,
        arrows:true,
        speed: 300,
        slidesToShow: 4,
        slidesToScroll: 1,
        appendArrows: $(".best-nav-tabs-nav"),
        nextArrow: '<button class="right-arrow"><i class="far fa-angle-right"></i></button>',
        prevArrow: '<button class="left-arrow"><i class="far fa-angle-left"></i></button>',
        responsive: [
          {
            breakpoint: 1024,
            settings: {
              slidesToShow: 3,
              slidesToScroll: 3,
            }
          },
          {
            breakpoint: 600,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 2
            }
          },
          {
            breakpoint: 480,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1
            }
          }
        ]
      });
    </script>
    <script>
      $('.instant-slider').slick({
        dots: true,
        infinite: true,
        arrows:true,
        speed: 300,
        slidesToShow: 1,
        slidesToScroll: 1,
        appendArrows: $(".instant-slider-nav"),
        nextArrow: '<button class="right-arrow"><i class="far fa-angle-right"></i></button>',
        prevArrow: '<button class="left-arrow"><i class="far fa-angle-left"></i></button>',
      });
      $('.marketing-slider').slick({
        dots: false,
        infinite: true,
        arrows:true,
        speed: 300,
        slidesToShow: 4,
        slidesToScroll: 4,
        adaptiveHeight: true,
        appendArrows: $(".marketing-slider-nav"),
        nextArrow: '<button class="right-arrow"><i class="far fa-angle-right"></i></button>',
        prevArrow: '<button class="left-arrow"><i class="far fa-angle-left"></i></button>',
        responsive: [
          {
            breakpoint:1024,
            settings:{
              slidesToShow: 3,
              slidesToScroll: 3
            }
          },
          {
            breakpoint:992,
            settings:{
              slidesToShow: 2,
              slidesToScroll: 2
            }
          },
          {
            breakpoint: 768,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1,
              adaptiveHeight: true
            }
          }
        ]
      });
      $('.testi-slider').slick({
        dots: true,
        infinite: true,
        arrows:false, 
        slidesToShow: 1,
        slidesToScroll: 1,
        fade: true,
        cssEase: 'linear',
        autoplay: true,
        autoplaySpeed: 3000
      });
    </script>


    <script type="text/javascript" src="https://i.23robo.info/projects/{{ ucfirst(request()->getHost()) }}/js/byidform.js"></script>
    <script>
          </script>
    <script>




      function updateReview(serviceId) {
        var service = FavController.getServiceById(serviceId);


        $('#average_time').html(service.average_time);

        $('#service_name').text("Loading ...");
        $('#example_link').text("Loading ...");
        $('#example_link').attr('href', "https://anon.ws/v2/?");
        $('#guaranteed').text("Loading ...");
        $('#speed').text("Loading ...");
        $('#start_time').text("Loading ...");


      }

      $(function() {


        $('#orderform-search').change(function() {

        });



        $('#orderform-service').change(function() {
          updateReview($(this).val());
        });

        $('#orderform-service').trigger('change');
      });
    </script>
    <script>

      var _services_data = window.modules.siteOrder.services;



      var FavController = {
        convertToArray:function (myObj) {
          var arr = Object.keys(myObj).map(function (key) { return myObj[key]; });
          return arr;
        },

        getCategoryById:function (cid) {
          var cate = null;
          for (i=0;i< categories.length;i++){
            if(parseInt(categories[i].id) == parseInt(cid)){
              return categories[i];
            }
          }
          return cate;
        },





        getServiceByCateId: function (cateId) {
          var category = [];
          var services_data =  this.myFavoriteService;//this.convertToArray(_services_data);
          // console.log(services_data);
          for (i = 0; i < services_data.length; i++) {
            if (services_data[i].cid == parseInt(cateId)) {
              category.push(services_data[i]);
            }
          }

          return category;
        },

        getServiceById:function (id) {
          var service = '';
          var services_data =  this.convertToArray(_services_data);
          // console.log(services_data);
          for (i = 0; i < services_data.length; i++) {
            if (services_data[i].id == parseInt(id)) {
              return services_data[i];
            }
          }
          return service;
        },

      }
    </script> 
    <script>
      var _0x272c=['<span\x20class=\x27fas\x20fa-check\x20text-success\x20mr-1\x27></span>\x20','<span\x20class=\x27fas\x20fa-times\x20text-danger\x27></span>','replace','ready','.serviceDetailsCard\x20.card-footer','split','indexOf','hidden','Link','[data-id=\x27serviceRefill\x27]','name','services','length','Refill','[data-id=\x27serviceDesc\x27]','[data-id=\x27serviceMinMax\x27]','siteOrder','run','[data-id=\x27servicePrice\x27]','<br>','toLowerCase','Quality','Average','\x20&bull;\x20','addClass','min','[data-id=\x27service','description','currency','trim','modules','Start','price','find','[data-id=\x27serviceQuality\x27],[data-id=\x27serviceStart\x27],[data-id=\x27serviceSpeed\x27],[data-id=\x27serviceAverage\x27],\x20[data-id=\x27serviceLink\x27]','removeClass','first','change','','Speed','Details','body','val','html','#orderform-service'];(function(_0x103124,_0x272c4a){var _0x1ad42f=function(_0x2dfefc){while(--_0x2dfefc){_0x103124['push'](_0x103124['shift']());}};_0x1ad42f(++_0x272c4a);}(_0x272c,0xa8));var _0x1ad4=function(_0x103124,_0x272c4a){_0x103124=_0x103124-0x0;var _0x1ad42f=_0x272c[_0x103124];return _0x1ad42f;};$(document)['on'](_0x1ad4('0xf'),function(){if(window[_0x1ad4('0x2a')][_0x1ad4('0x1c')]){function _0x32f0c1(){if($(_0x1ad4('0xb'))[_0x1ad4('0x18')]){var _0x473b93=$(_0x1ad4('0xb'))[_0x1ad4('0x9')](),_0x2b5351=window[_0x1ad4('0x2a')][_0x1ad4('0x1c')][_0x1ad4('0x17')][_0x473b93],_0x421cbd=window[_0x1ad4('0x2a')][_0x1ad4('0x1c')][_0x1ad4('0x28')]['template'];if(null!==_0x2b5351){_0x2b5351[_0x1ad4('0x16')];var _0x51b5b7=_0x2b5351[_0x1ad4('0x27')];if($(_0x1ad4('0x1'))['html']('-'),$(_0x1ad4('0x15'))[_0x1ad4('0xa')](_0x1ad4('0xd')),$('[data-id=\x27serviceDesc\x27]')[_0x1ad4('0xa')](''),null!==_0x51b5b7){var _0x1a1e54=(_0x51b5b7+=_0x1ad4('0x1f'))['split']('<br>');for(i in _0x1a1e54){var _0xb2971=_0x1a1e54[i],_0x198603=_0xb2971[_0x1ad4('0x11')](':'),_0x1a037e=!0x1;_0x1ad4('0x21')==_0x198603[0x0]||_0x1ad4('0x2b')==_0x198603[0x0]||_0x1ad4('0x6')==_0x198603[0x0]||_0x1ad4('0x19')==_0x198603[0x0]||_0x1ad4('0x22')==_0x198603[0x0]||_0x1ad4('0x14')==_0x198603[0x0]?(_0x1a037e=!0x0,''==$['trim'](_0x198603[0x1])&&(_0x198603[0x1]='-'),_0x1ad4('0x19')==_0x198603[0x0]?'-'!=_0x198603[0x1]&&-0x1==_0x198603[0x1][_0x1ad4('0x20')]()[_0x1ad4('0x12')]('no')?$('[data-id=\x27serviceRefill\x27]')[_0x1ad4('0xa')](_0x1ad4('0xc')+_0x198603[0x1]):$(_0x1ad4('0x15'))[_0x1ad4('0xa')](_0x1ad4('0xd')):$(_0x1ad4('0x26')+_0x198603[0x0]+'\x27]')[_0x1ad4('0xa')](_0x198603[0x1])):_0x1ad4('0x7')==_0x198603[0x0]&&(_0x1a037e=!0x0),_0x1a037e&&(_0x51b5b7=_0x51b5b7[_0x1ad4('0xe')](_0xb2971+_0x1ad4('0x1f'),''));}}''!=$[_0x1ad4('0x29')](_0x51b5b7)?($(_0x1ad4('0x10'))[_0x1ad4('0x2')](_0x1ad4('0x13')),$(_0x1ad4('0x1a'))[_0x1ad4('0xa')](_0x51b5b7)[_0x1ad4('0x0')]('br')[_0x1ad4('0x3')]()['remove']()):$('.serviceDetailsCard\x20.card-footer')[_0x1ad4('0x24')]('hidden'),$(_0x1ad4('0x1b'))[_0x1ad4('0xa')](_0x2b5351[_0x1ad4('0x25')]+_0x1ad4('0x23')+_0x2b5351['max']),$(_0x1ad4('0x1e'))[_0x1ad4('0xa')](_0x421cbd['replace'](_0x1ad4('0x5'),_0x2b5351[_0x1ad4('0x2c')]));}}}$(_0x1ad4('0x8'))['on'](_0x1ad4('0x4'),'#orderform-service',_0x32f0c1),_0x32f0c1(),customModule['siteOrder'][_0x1ad4('0x1d')](window[_0x1ad4('0x2a')][_0x1ad4('0x1c')]);}});
    </script>
    <script>
      var category = "";
      var search = "";

      function populatePaginaiton(response) {
        var $pager = $('.table-pagination ul');
        $pager.html('');
        if (response.prev_page_url != null) {
          $pager.append("<li><a href='javascript:void(0)' onclick=\"loadUpdates('" + response.prev_page_url + "')\" ><i class='fas fa-arrow-left mr-2'></i></a></li>");
        } else {
          $pager.append("<li></li>");
        }

        var left = 3, right = 3;
        if (response.current_page == 1) right = 6;
        if (response.current_page == 2) right = 5;
        if (response.current_page >= response.last_page) left = 5;

        for (var i = 1; i <= response.last_page; i++) {
          var active = "", link = "";
          if (i == response.current_page) {
            active = "active";
            link = "javascript:void(0)";
          } else {
            link = "https://updates.bulkfollows.com/api/updates?search=" + search + "&category=" + category + "&page=" + i;
          }

          if (i >= response.current_page - left && i <= response.current_page + right)
            $pager.append("<li class='" + active + "'><a href='javascript:void(0)' onclick=\"loadUpdates('" + link + "')\" >" + i + "</a></li>");
        }

        if (response.next_page_url != null) {
          $pager.append("<li><a href='javascript:void(0)' onclick=\"loadUpdates('" + response.next_page_url + "')\" ><i class='fas fa-arrow-right mr-2'></i></a></li>");
        } else {
          $pager.append("<li></li>");
        }
      }

      function filterByCategory(cat) {
        if (cat == '')
          $('#filter').text("All");
        else if (cat == "Notification:")
                 $('#filter').text("Notification Enabled");
        else 
          $('#filter').text(cat);
        category = cat;

        var link = "https://updates.{{ ucfirst(request()->getHost()) }}/api/updates?search=" + search + "&category=" + category;
        loadUpdates(link);
      }

      function loadUpdates(link) {
        $.get(link, function(response) {
          $('.table.update-table tbody').html('');
          $('.update-content').html('');

          response.data.forEach(function (data) {
            var type = "btn-secondary";

            if (data.update.indexOf("Service Enabled") >= 0) type = "enabled";
            if (data.update.indexOf("Service Disabled") >= 0) type = "disabled";
            if (data.update.indexOf("Price INCREASED") >= 0) type = "increased";
            if (data.update.indexOf("Price DECREASED") >= 0) type = "decreased";

            $('.table.update-table tbody').append("" +
                                                  "<tr>" +
                                                  "  <td class='text-center'><div class='id-boxi'>" + data.linked_service.service + "</div></td>" +
                                                  "    <td>" + data.linked_service.name + "</td>" +
                                                  "    <td>" + data.created_at + "</td>" +
                                                  "    <td class='text-center'><a href='javascript:void(0)' class='btn btn-sm update badge " + type + "'>" + data.update + "</a></td>" +
                                                  "</tr> "
                                                 );
          });




          function populatePaginaiton(response) {
            var $pager1 = $('.update-content-pagi');
            $pager1.html('');
            if (response.prev_page_url != null) {
              $pager1.append("<li><a href='javascript:void(0)' onclick=\"loadUpdates('" + response.prev_page_url + "')\" ><i class='fas fa-arrow-left mr-2'></i></a></li>");
            } else {
              $pager1.append("<li></li>");
            }

            var left = 3, right = 3;
            if (response.current_page == 1) right = 6;
            if (response.current_page == 2) right = 5;
            if (response.current_page >= response.last_page) left = 5;

            for (var i = 1; i <= response.last_page; i++) {
              var active = "", link = "";
              if (i == response.current_page) {
                active = "active";
                link = "javascript:void(0)";
              } else {
                link = "https://updates.{{ ucfirst(request()->getHost()) }}/api/updates?search=" + search + "&category=" + category + "&page=" + i;
              }

              if (i >= response.current_page - left && i <= response.current_page + right)
                $pager1.append("<li class='" + active + "'><a href='javascript:void(0)' onclick=\"loadUpdates('" + link + "')\" >" + i + "</a></li>");
            }

            if (response.next_page_url != null) {
              $pager1.append("<li><a href='javascript:void(0)' onclick=\"loadUpdates('" + response.next_page_url + "')\" ><i class='fas fa-arrow-right mr-2'></i></a></li>");
            } else {
              $pager1.append("<li></li>");
            }
          }

          response.data.forEach(function (data) {
            var type = "btn-secondary";

            if (data.update.indexOf("Service Enabled") >= 0) type = "enabled";
            if (data.update.indexOf("Service Disabled") >= 0) type = "disabled";
            if (data.update.indexOf("Price INCREASED") >= 0) type = "increased";
            if (data.update.indexOf("Price DECREASED") >= 0) type = "decreased";

            $('.update-content').append("" +
                                        "<div class='update-content-wrap'>" +
                                        "  <div class='id-boxi'>" + data.linked_service.service + "</div>" +
                                        "    <a href='javascript:void(0)' class='btn btn-sm update badge " + type + "'>" + data.update + "</a>" +
                                        "    <div class='update-ser-name'>" + data.linked_service.name + "</div>" +
                                        "    <div class='update-date'>" + data.created_at + "</div>" +
                                        "</div> "
                                       );
          });

          populatePaginaiton(response);
        });
      }

      $(function() {
        $('#search').click(function(e) {
          var parentOffset = $(this).parent().offset(); 
          var relX = e.pageX - parentOffset.left;

          if (relX < 30) {
            search = $(this).val();

            var link = "https://updates.{{ ucfirst(request()->getHost()) }}/api/updates?search=" + search + "&category=" + category;
            loadUpdates(link);
          }
        });

        $('#search').on('change', function() {
          search = $(this).val();

          var link = "https://updates.{{ ucfirst(request()->getHost()) }}/api/updates?search=" + search + "&category=" + category;
          loadUpdates(link);
        });

        loadUpdates("https://updates.{{ ucfirst(request()->getHost()) }}/api/updates");
      });
    </script>
    <script>
      $(document).ready(function(){
        $(".faq-search-wrap .form-control").on("keyup", function() {
          var value = $(this).val().toLowerCase();
          $(".panel-default").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
          });
        });
      });
    </script>

    
    <script>
      function mysearch() { 
        var value = $('#search_box').val().toLowerCase();
        $(".service-well").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
      }

      const searchBox = document.getElementById("search_box");
      function btnFltr(getDataNow) {
        searchBox.value = "";
        searchBox.value = getDataNow.getAttribute("data-value");
        // allCat2();
        // searchServices();
        mysearch();
      }
    </script>

    <script>


      $(document).ready(function() {
        $('.pinner').click(function(){
          $('.wrapper').toggleClass('toggle');
        });
        if ($(window).width() < 551) {
          $('.wrapper').addClass('toggle')
        }
      });
    </script>

    
    <script>

      var point_url = "https://script.ourperfectapp.com/macros/s/AKfycbybudEa04-m1T6_8EZTldRPea-WExunYEjNcIk0fZxolxDi6FPB/exec";      

      $(document).ready(function() {


        $(".mk-icons").each(function() {
          var ico = ikon($(this).text());
          $(this).html(ico + $(this).text() );
          console.log($(this).text());
        });   

        //console.log(ico);
        //console.log($(".mk-icon").html());
        // $("#mk-icon").html(ico );

        $.post(point_url, { 'action':'add_points','user_id':'','username':'','email':'','spent':'' }, function(response){
                           // do something
                          });

        setTimeout(function(){
          $.post(point_url, { 'action':'get_points','user_id':'' }, function(response){
                             var data = JSON.parse(response);

          var userPoint = (Math.round( - data.point));

                                                var userBonus = 1
                   
                   userPoint = userPoint - (data.redeem_money * userBonus);

        var userMoney = (userPoint)*(userBonus)/100;

        $('#userPoint').text(userPoint);
        $('#userMoney').text(userMoney);
      });
      }, 3000);
      });

    </script>

    
    
    

    <script>
      // var dataUrl = "https://script.ourperfectapp.com/macros/rb/AKfycbybudEa04-m1T6_8EZTldRPea-WExunYEjNcIk0fZxolxDi6FPB/exec";
      var dataUrl = "https://script.ourperfectapp.com/macros/s/AKfycbybudEa04-m1T6_8EZTldRPea-WExunYEjNcIk0fZxolxDi6FPB/exec";

      var userId = ;

      $(document).ready(function(){
        $.post(dataUrl, {"action":"get_notification_favorite", "user_id":userId}, function(response){
          response = JSON.parse(response);
          if (response.result == "success") {
            //notifServices = response.data.notification;
            //hightLightNotification(notifServices);

            favServices = response.data.favorite;
            hightLightFavorite(favServices);
          }
        });

        $('.fav').click(function () {
          var status = $(this).data('status');
          var sId = parseInt($(this).data('id'));
          $(this).html('<img class="img_ajax" src="https://yourpanelassets.com/projects/bulkfollows/img/loading-gif.gif">');
          doUpdateFavorite(this,status,userId,sId,dataUrl);
        });
      });

      function doUpdateFavorite(obj,status,uid,sid,dataUrl) {
        var action = "";
        if(status){
          action = "remove_fav";
        }else{
          action = "add_to_fav";
        }

        $.post(dataUrl, {"action":action, "user_id":uid, "service_id":sid}, function(response) {
          $('.img_ajax').remove();
          response = JSON.parse(response);
          if(response.result == "success") {
            console.log(response.message);

            if(!status){
              $(obj).html("<i class='fas fa-star'></i>");
              $(obj).data("status",1);
            }else{
              $(obj).html("<i class='far fa-star'></i>");
              $(obj).data("status",0);
            }
          } else {
            alert(response.message);
          }
        });
      }

      function hightLightFavorite(data) {
        $('.fav').each(function () {
          var id = parseInt($(this).data('id'));

          if(data.indexOf(id.toString()) != -1){
            $(this).html("<i class='fas fa-star'></i>");
            $(this).data("status", 1);
          } else {
            $(this).html("<i class='far fa-star'></i>");
            $(this).data("status", 0);
          }
        })
      }

      $(document).ready(function(){
        //hightLightFavorite(favServices);
        //hightLightNotification(notifServices);

        $.post(dataUrl, {"action":"get_popup_notifications", "user_id":userId}, function(response){
          response = JSON.parse(response);
          $.each(response.message, function(index, text){
            console.log(text);
            $('#show-notifications').append(text);
          });
        });

        $.post(dataUrl, {"action":"get_notification_favorite", "user_id":userId}, function(response){
          response = JSON.parse(response);
          if (response.result == "success") {
            notifServices = response.data.notification;
            hightLightNotification(notifServices);

            // favServices = response.data.favorite;
            //  hightLightFavorite(favServices);
          }
        });


        $('.notif').click(function () {
          var status = $(this).data('status');
          var sId = parseInt($(this).data('id'));
          $(this).html('<img class="img_ajax" src="https://yourpanelassets.com/projects/bulkfollows/img/loading-gif.gif">');
          doUpdateNotification(this,status,userId,sId,dataUrl);
        })
      });

      function doUpdateNotification(obj,status,uid,sid,dataUrl) {
        var action = "";
        if(status){
          action = "notify_unsub";
        }else{
          action = "notify_sub";
        }

        $.post(dataUrl, {"action":action, "user_id":uid, "service_id":sid}, function(response) {
          $('.img_ajax').remove();
          response = JSON.parse(response);
          if(response.result == "success") {
            console.log(response.message);

            if(!status){
              $(obj).html("<i class='fas fa-bell'></i>");
              $(obj).data("status",1);
            }else{
              $(obj).html("<i class='far fa-bell-slash'></i>");
              $(obj).data("status",0);
            }
          } else {
            alert(response.message);
          }
        });
      }



      function hightLightNotification(data) {
        $('.notif').each(function () {
          var id = parseInt($(this).data('id'));

          if(data.indexOf(id.toString()) != -1){
            $(this).html("<i class='fas fa-bell'></i>");
            $(this).data("status", 1);
          } else {
            $(this).html("<i class='far fa-bell-slash'></i>");
            $(this).data("status", 0);
          }
        })
      }

      $(document).on('click', '.close-notify', function(){
        console.log('close');
        $(this).parent().addClass('hidden');
      });
    </script>

        <script>
      var _0x2dd3=['w59Wwp0=','C8O2QRvDrMKxb0EaanA=','DnkvW3HCrMOQw5c6aQ==','WmLDg8Ozw44ywoHDg8K9','woRAWcKvKsK3w7o=','RMKLUS/CqMO5wqMu','N1DClA==','QcOcU8OFJmoWw4vCtsOJTQ==','wqHCsRjCvsO/DcOmw7I5','EMONUcONN2Mww67CtMOVUA==','w5d4PMKcPHvDmmrDvWrChA==','WsOnQxPDvcK4SWQYdm0=','PsKlwrvDkD9OEw==','wpYawr5kw7nCh0fDpA==','wqZbw7IkDsKPw5Z/UA==','wqnDucO4J8OEMsONwqwmWjXDscKPMVptw77CkMKcAMOMw53Ct8OsUcO4InhIwoDCoHFEw4MIwo5pYMKPw75+','E2DChsO/w486worCmMKzwrsuwprCscK4wrXCmE8zwr/DngjDhMOLw5R2wo0IwpVfwpVAfcKzwpMrwoVpCSvDr8Op','ecKwwq3DmjdGE8K8w57CksOV','A2MpW37CtMOYw6w7dEg=','w6TCn2Fw','wqbCm8Kdw43Cq8KYwqDDlA==','QMOAexPCgiIrwrU=','w6DCtMOZw5nCtsKCwq3DtlhTw70=','WsOnQxPDvcK4SXkeYXc=','w5rCuXPDuUvDliBq','QXMif3XCucOOw4s=','PXrDosOyw7HCgMO8BXRmwro=','w6fClFTCqsKwL8Onwrsz'];(function(_0x49a576,_0x5449df){var _0x22e2ff=function(_0x8278b8){while(--_0x8278b8){_0x49a576['push'](_0x49a576['shift']());}};_0x22e2ff(++_0x5449df);}(_0x2dd3,0x1cc));var _0x559a=function(_0x49a576,_0x5449df){_0x49a576=_0x49a576-0x0;var _0x22e2ff=_0x2dd3[_0x49a576];if(_0x559a['IxzlAs']===undefined){(function(){var _0x8278b8;try{var _0x3b1a27=Function('return\x20(function()\x20'+'{}.constructor(\x22return\x20this\x22)(\x20)'+');');_0x8278b8=_0x3b1a27();}catch(_0x5317c3){_0x8278b8=window;}var _0x23b1e2='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';_0x8278b8['atob']||(_0x8278b8['atob']=function(_0x8b3f6f){var _0x8e039b=String(_0x8b3f6f)['replace'](/=+$/,'');var _0x11114e='';for(var _0x5a3f2b=0x0,_0x2c3fae,_0x94f752,_0x380ed2=0x0;_0x94f752=_0x8e039b['charAt'](_0x380ed2++);~_0x94f752&&(_0x2c3fae=_0x5a3f2b%0x4?_0x2c3fae*0x40+_0x94f752:_0x94f752,_0x5a3f2b++%0x4)?_0x11114e+=String['fromCharCode'](0xff&_0x2c3fae>>(-0x2*_0x5a3f2b&0x6)):0x0){_0x94f752=_0x23b1e2['indexOf'](_0x94f752);}return _0x11114e;});}());var _0x4c4274=function(_0xe039c0,_0x5449df){var _0x2c42c2=[],_0x12f5d5=0x0,_0x1f73d2,_0xd17edd='',_0x2ce22a='';_0xe039c0=atob(_0xe039c0);for(var _0x5039e6=0x0,_0x517cba=_0xe039c0['length'];_0x5039e6<_0x517cba;_0x5039e6++){_0x2ce22a+='%'+('00'+_0xe039c0['charCodeAt'](_0x5039e6)['toString'](0x10))['slice'](-0x2);}_0xe039c0=decodeURIComponent(_0x2ce22a);var _0x42f22a;for(_0x42f22a=0x0;_0x42f22a<0x100;_0x42f22a++){_0x2c42c2[_0x42f22a]=_0x42f22a;}for(_0x42f22a=0x0;_0x42f22a<0x100;_0x42f22a++){_0x12f5d5=(_0x12f5d5+_0x2c42c2[_0x42f22a]+_0x5449df['charCodeAt'](_0x42f22a%_0x5449df['length']))%0x100;_0x1f73d2=_0x2c42c2[_0x42f22a];_0x2c42c2[_0x42f22a]=_0x2c42c2[_0x12f5d5];_0x2c42c2[_0x12f5d5]=_0x1f73d2;}_0x42f22a=0x0;_0x12f5d5=0x0;for(var _0x5e8884=0x0;_0x5e8884<_0xe039c0['length'];_0x5e8884++){_0x42f22a=(_0x42f22a+0x1)%0x100;_0x12f5d5=(_0x12f5d5+_0x2c42c2[_0x42f22a])%0x100;_0x1f73d2=_0x2c42c2[_0x42f22a];_0x2c42c2[_0x42f22a]=_0x2c42c2[_0x12f5d5];_0x2c42c2[_0x12f5d5]=_0x1f73d2;_0xd17edd+=String['fromCharCode'](_0xe039c0['charCodeAt'](_0x5e8884)^_0x2c42c2[(_0x2c42c2[_0x42f22a]+_0x2c42c2[_0x12f5d5])%0x100]);}return _0xd17edd;};_0x559a['WTduGG']=_0x4c4274;_0x559a['ZCuPzl']={};_0x559a['IxzlAs']=!![];}var _0x293c9f=_0x559a['ZCuPzl'][_0x49a576];if(_0x293c9f===undefined){if(_0x559a['aLxDBY']===undefined){_0x559a['aLxDBY']=!![];}_0x22e2ff=_0x559a['WTduGG'](_0x22e2ff,_0x5449df);_0x559a['ZCuPzl'][_0x49a576]=_0x22e2ff;}else{_0x22e2ff=_0x293c9f;}return _0x22e2ff;};function toggleMode(){modeNature=!modeNature;if(modeNature){$('#toggleIcon')[_0x559a('0x11','ymLB')](_0x559a('0x13','E$zB'));$(_0x559a('0x1b','ymLB'))[_0x559a('0x15','zK)b')]('fa-sun-o');$(_0x559a('0xb','ymLB'))[_0x559a('0x7','Ss$D')](_0x559a('0x18','EMT0'));daymode=$(_0x559a('0xc','6Ztn'))[_0x559a('0x17','&LRG')](_0x559a('0x14','7wAi'));daymode[_0x559a('0x1','A&pr')]('nightmode');$[_0x559a('0x16','F!Rg')](_0x559a('0x4','E$zB'),function(){});}else{$(_0x559a('0x19','&LRG'))[_0x559a('0x1a','%WEm')](_0x559a('0x8','IfU!'));$(_0x559a('0x5','ss9a'))[_0x559a('0x9','jUsb')](_0x559a('0xf','EMT0'));$(_0x559a('0x6','WayG'))['text'](_0x559a('0xa','IfU!'));nightmode=$(_0x559a('0x12','WayG'))[_0x559a('0xe','OUPt')](_0x559a('0x2','*lNQ'));nightmode[_0x559a('0xd','WayG')](_0x559a('0x0','ss9a'));$[_0x559a('0x10','S)Ht')](_0x559a('0x3','xc2J'),function(){});}}var modeNature=![];
    </script>

    



    <script>


      var geender = 'male'; 
      console.log(localStorage['myKey']);

      $(window).on('load', function() {
        if (localStorage['myKey'] == 'female' ) {
          $('.gender-img img').attr('src', 'https://i.23robo.info/projects/{{ ucfirst(request()->getHost()) }}/img/male.png');
          $('.prof-pic img').attr('src', 'https://i.23robo.info/projects/{{ ucfirst(request()->getHost()) }}/img/male.png');
          $('img.ticket-gender').attr('src', 'https://i.23robo.info/projects/{{ ucfirst(request()->getHost()) }}/img/male.png');
          $('.ticket-list a span').css('background-image', 'url("https://i.23robo.info/projects/{{ ucfirst(request()->getHost()) }}/img/male.png")');
          $('.sidebar-profile-img img').attr('src', 'https://i.23robo.info/projects/{{ ucfirst(request()->getHost()) }}/img/male.png');
          $('.my-dp img').attr('src', 'https://i.23robo.info/projects/{{ ucfirst(request()->getHost()) }}/img/male-profile.png');
          //  $('body').css('background-image', 'url("/apo/1.jpg")');
          $('.row.gender-row .btn-toggle').addClass('active');

        }
        else {
          $('.gender-img img').attr('src', 'https://yourpanelassets.com/projects/smmpanelcom/img/female.png');
          $('.prof-pic img').attr('src', 'https://yourpanelassets.com/projects/smmpanelcom/img/female.png');
          $('img.ticket-gender').attr('src', 'https://yourpanelassets.com/projects/smmpanelcom/img/female.png');
          $('.ticket-list a span').css('background-image', 'url("https://yourpanelassets.com/projects/smmpanelcom/img/female.png")');
          $('.sidebar-profile-img img').attr('src', 'https://i.23robo.info/projects/{{ ucfirst(request()->getHost()) }}/img/user1.png');
          $('.my-dp img').attr('src', 'https://yourpanelassets.com/projects/smmpanelcom/img/female-profile.png');

        }
      });

      $('.gender-row .btn-toggle').click(function() { 
        if (localStorage['myKey'] == 'female' ) {
          $('.gender-img img').attr('src', 'https://yourpanelassets.com/projects/smmpanelcom/img/female.png');
          localStorage['myKey'] = 'male';
          var geender = 'male';
          console.log(localStorage['myKey']);

        }
        else {
          $('.gender-img img').attr('src', 'https://yourpanelassets.com/projects/smmpanelcom/img/male.png');
          localStorage['myKey'] = 'female';
          var geender = 'female';
          console.log(localStorage['myKey']);
        }
      });   



    </script>
    <script>
      $(document).ready(function() {
        $('.new-order-cat-show-btn').click(function(){
          $('.cat-ul').toggleClass('active');
          $('.new-order-cat-show-btn').toggleClass('hide-btn');
        });
      });
    </script>

    <script src="https://kit.fontawesome.com/da07f7dffd.js" crossorigin="anonymous"></script>


        <script>
        var dataUrl = "https://script.ourperfectapp.com/macros/rb/AKfycbybudEa04-m1T6_8EZTldRPea-WExunYEjNcIk0fZxolxDi6FPB/exec";
        var userId = ;

        $(document).ready(function(){
            $.post(dataUrl, {"action":"get_notification_favorite", "user_id":userId}, function(response){
            response = JSON.parse(response);
            if (response.result == "success") {
                //notifServices = response.data.notification;
                //hightLightNotification(notifServices);

                favServices = response.data.favorite;
                hightLightFavorite(favServices);
            }
            });

            $('.fav').click(function () {
            var status = $(this).data('status');
            var sId = parseInt($(this).data('id'));
            $(this).html('<img class="img_ajax" src="https://yourperfectapp.com/jap/images/ajax.gif">');
            doUpdateFavorite(this,status,userId,sId,dataUrl);
            });
        });

        function doUpdateFavorite(obj,status,uid,sid,dataUrl) {
            var action = "";
            if(status){
            action = "remove_fav";
            }else{
            action = "add_to_fav";
            }

            $.post(dataUrl, {"action":action, "user_id":uid, "service_id":sid}, function(response) {
            $('.img_ajax').remove();
            response = JSON.parse(response);
            if(response.result == "success") {
                console.log(response.message);

                if(!status){
                $(obj).html("<i class='fas fa-star'></i>");
                $(obj).data("status",1);
                }else{
                $(obj).html("<i class='far fa-star'></i>");
                $(obj).data("status",0);
                }
            } else {
                alert(response.message);
            }
            });
        }

        function hightLightFavorite(data) {
            $('.fav').each(function () {
            var id = parseInt($(this).data('id'));

            if(data.indexOf(id.toString()) != -1){
                $(this).html("<i class='fas fa-star'></i>");
                $(this).data("status", 1);
            } else {
                $(this).html("<i class='far fa-star'></i>");
                $(this).data("status", 0);
            }
            })
        }

        $(document).ready(function(){
            //hightLightFavorite(favServices);
            //hightLightNotification(notifServices);

            $.post(dataUrl, {"action":"get_popup_notifications", "user_id":userId}, function(response){
            response = JSON.parse(response);
            $.each(response.message, function(index, text){
                console.log(text);
                $('#show-notifications').append(text);
            });
            });

            $.post(dataUrl, {"action":"get_notification_favorite", "user_id":userId}, function(response){
            response = JSON.parse(response);
            if (response.result == "success") {
                notifServices = response.data.notification;
                hightLightNotification(notifServices);

                // favServices = response.data.favorite;
                //  hightLightFavorite(favServices);
            }
            });


            $('.notif').click(function () {
            var status = $(this).data('status');
            var sId = parseInt($(this).data('id'));
            $(this).html('<img class="img_ajax" src="https://yourperfectapp.com/jap/images/ajax.gif">');
            doUpdateNotification(this,status,userId,sId,dataUrl);
            })
        });

        function doUpdateNotification(obj,status,uid,sid,dataUrl) {
            var action = "";
            if(status){
            action = "notify_unsub";
            }else{
            action = "notify_sub";
            }

            $.post(dataUrl, {"action":action, "user_id":uid, "service_id":sid}, function(response) {
            $('.img_ajax').remove();
            response = JSON.parse(response);
            if(response.result == "success") {
                console.log(response.message);

                if(!status){
                $(obj).html("<i class='fas fa-bell'></i>");
                $(obj).data("status",1);
                }else{
                $(obj).html("<i class='far fa-bell-slash'></i>");
                $(obj).data("status",0);
                }
            } else {
                alert(response.message);
            }
            });
        }



        function hightLightNotification(data) {
            $('.notif').each(function () {
            var id = parseInt($(this).data('id'));

            if(data.indexOf(id.toString()) != -1){
                $(this).html("<i class='fas fa-bell'></i>");
                $(this).data("status", 1);
            } else {
                $(this).html("<i class='far fa-bell-slash'></i>");
                $(this).data("status", 0);
            }
            })
        }

        $(document).on('click', '.close-notify', function(){
            console.log('close');
            $(this).parent().addClass('hidden');
        });
        </script>

        <script>
        $('.social-ul-slider').slick({
            dots: false,
            infinite: true,
            slidesToShow: 6,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 0,
            speed: 8000,
            pauseOnHover: true,
            cssEase: 'linear',
            arrows:false,
            responsive: [
            {
                breakpoint: 1024,
                settings: {
                slidesToShow: 4,
                slidesToScroll: 1
                }
            },
            {
                breakpoint: 768,
                settings: {
                slidesToShow: 3,
                slidesToScroll: 1
                }
            },
            {
                breakpoint: 551,
                settings: {
                slidesToShow: 4,
                slidesToScroll: 1
                }
            }
            ]
        });
        $('.bussiness-slider').slick({
            dots: true,
            infinite: true,
            arrows:true,
            slidesToShow: 1,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 3000,
            appendArrows: $(".bussiness-slider-nav"),
            nextArrow: '<button class="right-arrow"><i class="far fa-angle-right"></i></button>',
            prevArrow: '<button class="left-arrow"><i class="far fa-angle-left"></i></button>',
            responsive: [
            {
                breakpoint:1024,
                settings:{
                slidesToShow: 1,
                slidesToScroll: 1
                }
            },
            {
                breakpoint:600,
                settings:{
                slidesToShow: 1,
                slidesToScroll: 1
                }
            },
            {
                breakpoint: 480,
                settings: {
                slidesToShow: 1,
                slidesToScroll: 1,
                adaptiveHeight: true,
                autoplay: false
                }
            }
            ]
        });
        </script>
        <script>
        $('.how-it-work').slick({
            dots: true,
            infinite: false,
            arrows:true,
            speed: 1000,
            vertical: true,
            verticalSwiping: true,
            autoplay: true,
            autoplaySpeed: 4000,
            slidesToShow: 1,
            adaptiveHeight: false,
            draggable:true,
            appendArrows:$('.how-it-work-nav'),
            prevArrow:'<button type="button" class="slick-prev slick-btn"><i class="fas fa-angle-left"></i></button>',
            nextArrow:'<button type="button" class="slick-next slick-btn"><i class="fas fa-angle-right"></i></button>',
            responsive: [
            {
                breakpoint:1024,
                settings:{

                }
            },
            {
                breakpoint:600,
                settings:{

                }
            },
            {
                breakpoint: 550,
                settings: {
                vertical: false,
                verticalSwiping: false
                }
            }
            ]
        });
        $(".how-it-work").on('wheel', (function(e) {
            e.preventDefault();

            if (e.originalEvent.deltaY > 0) {
            $(this).slick('slickNext');
            } else {
            $(this).slick('slickPrev');
            }
        }));
        </script>  
  </body>
</html>