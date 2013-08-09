<?xml version="1.0"?>

<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:soc="http://kemmerer.co">
    <xsl:output method="xml" omit-xml-declaration="yes"/>

    <xsl:template match="/Header">
        <xsl:text disable-output-escaping='yes'>&lt;!DOCTYPE html&gt;</xsl:text>
        <html>
            <head>
                <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
                <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
                <meta http-equiv="Pragma" content="no-cache" />
                <meta http-equiv="Expires" content="0" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
                <script type = "text/javascript" src="/Static/JavaScript/Plugins/mixpanel.js"></script>
                <title>The Socialer</title>
                <xsl:apply-templates select="CSS/file" />
                <soc:googleAnalytics environment="dev" />
                <link rel="icon" href="/Photos/favicon.ico" type="image/x-icon" />
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
                <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&amp;sensor=false&amp;libraries=places"></script>
               
            </head>
            <body class="Socialer">


                <div id="fb-root"></div>
                <script src="/Static/JavaScript/Plugins/facebook.js"></script>


                <div id="Header">
                    <div class="pane-content">
                        <span class="social-link">
                            <a  data-toggle="tooltip" data-placement='bottom' id='Twitter' href="https://twitter.com/@socialerphilly" title="Check out our Twitter!" >L</a>
                        </span>
                        <span class="social-link">
                            <a data-toggle="tooltip" data-placement='top'  id='Facebook' href="https://www.facebook.com/TheSocialer" title="Check out our Facebook!">F</a>
                        </span>
                        <span class="social-link">
                            <a  id='Blog' href="http://socialerblog.com/" title="Check out our blog!">F</a>
                        </span>
                        
                    </div>
                    <a href="/">
                        
                        <div>
                            <div id='the'>the</div>
                            <div id='company_name'> Socialer</div>
                        </div>
                        <!-- <img title="The Socialer" src="/Static/Images/Logo.jpg" id="Logo" /> -->
                        <span id="Beta"> BETA </span>
                    </a>
                    <div id="AccountSettingsContainer">
                        <xsl:choose>
                            <xsl:when test="./Viewer/@userId != -1">
                               
                                <div class="dropdown">
                                    <a class="dropdown-toggle" id="dLabel" data-toggle="dropdown" data-target="#">
                                        <xsl:value-of select="./Viewer/Member/@firstName" />

                                        <b class="caret"></b>
                                    </a>
                                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                                        <li>
                                            <a href="/profile">
                                                <img width='25' height='25' src="/photo/{./Viewer/@userId}/Small"/> 
                                                <div class='profile'>Profile</div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="/messages">
                                                <img width='25' src="/Static/Images/mailbox.png"/>
                                                <div class='profile'>Messages</div>

                                                <xsl:if test="@unreadCount > 0">
                                                    <span class="alert-count position-super">
                                                        <xsl:value-of select="@unreadCount" />
                                                    </span>
                                                </xsl:if>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="/network" id='network'>
                                                <img width='25' height='auto' src="/Static/Images/connections_white.png"/>
                                                <div class='profile'> Connections</div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="/settings" id='settings'>
                                                <img width='25' height='25' src="/Static/Images/settings_white.png"/>
                                                <div class='profile'>Settings</div>
                                            </a>
                                        </li>
                                        <li>
                                            <xsl:choose>
                                                <xsl:when test="./Viewer/Member/@fb_id">
                                        
                                                    <a href="#" id="fb_logout">
                                                        <img width='25' height='auto' src="/Static/Images/logout.png"/>
                                                        <div class='profile'>Log Out</div>
                                                    </a>
                                                </xsl:when>
                                                <xsl:otherwise>
                                        
                                                    <a href="/logout" >
                                                        <img width='25' height='auto' src="/Static/Images/logout.png"/>
                                                        <div class='profile'>Log Out</div>
                                                    </a>
                                                </xsl:otherwise>
                                            </xsl:choose>
                                        </li>
                                    </ul>
                                </div>
                                <div id="SocialInbox">
                                    <div class="clickRegion">
                                        <span>Social Inbox</span>
                                        <div id="SocialInboxCount">
                                            <xsl:value-of select="count(SocialInbox/FriendRequests/FriendRequest)+count(SocialInbox/LocationShares/LocationShare)" />
                                            <b class="caret"></b>
                                        </div>
                                    </div>
                                    <ol id="SocialInboxNotifications">
                                        <xsl:choose>
                                            <xsl:when test="count(SocialInbox/FriendRequests/FriendRequest) > 0"> 
                                                <xsl:apply-templates select="SocialInbox/FriendRequests" />
                                            </xsl:when>
                                            <xsl:otherwise>
                                                <li class="Notification none">No new friend requests.</li>
                                            </xsl:otherwise>
                                        </xsl:choose>
                                        <xsl:choose>
                                            <xsl:when test="count(SocialInbox/LocationShares/LocationShare) > 0"> 
                                                <xsl:apply-templates select="SocialInbox/LocationShares" />
                                            </xsl:when>
                                            <xsl:otherwise>
                                                <li class="Notification none">No new shares.</li>
                                            </xsl:otherwise>
                                        </xsl:choose>
                                    </ol>
                                </div>
        
                              
                                
                            </xsl:when>
                            <xsl:otherwise>
                                <a href="#myModal" role="button" class="btn" data-toggle="modal" >Login</a>
                                <span>|</span>
                                <a href="#myModal2" role="button" class="btn" data-toggle="modal" >Sign Up</a>
                            </xsl:otherwise>
                        </xsl:choose>
                    </div>
                    
                </div>
                <div id="Navigation">
                    <xsl:call-template name="LoggedInNavigation" />

                  <!--  <xsl:call-template name="HowItWorks" />-->
                </div>
                <xsl:value-of select="markup" disable-output-escaping="yes" />
 
                <!-- Modal login-->
                <div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

                    <div id="MemberLoginContainer">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3>Already a member?</h3> Login. Don't have an account? <a href="#myModal2" role="button" class="btn" data-toggle="modal">Sign up.</a>

                        <hr/>
                        <br/>
                        <div id='fb-connect' class="fb-login">
                            <div id='left'>
                                <i class="icon-facebook icon-large"></i>
                            </div>
                            <span id='border'></span>
                            <div id='right'>Connect with Facebook</div>
                        </div>

                        <h3 id='or'> or </h3>
                        <div id="QuickLoginFormLoginFailed"></div>
                        <div id="tryagain">Your search did not return any results. Please try again with another email.</div>
                        <br id="break"  style="display:none;"/>
                        <form id="QuickLoginForm">
                            <input type="email" name="LoginEmail" placeholder="Email" />
                            <div class="pass">
                                <input type="password" name="LoginPassword" placeholder="Password" />
                                <div class="ForgotLink">?</div>
                            </div>
                            <br/>
                            <div class="remember">
                                <label for="remem">
                                    <input id="remem" type="checkbox" checked="checked"/>
                                    <span>Keep me logged in</span>
                                </label>
 
                            </div>
                            <input class="joinbutton" id="QuickLoginLink" type="submit" value="Log In" />
                        </form>
                
            
                        <form id="ForgotPasswordForm" class="hide" autocomplete="off" method="post" action="">
                            <xsl:text>Forgot your password? Just give us your email and we'll send you instructions on how to reset it. </xsl:text>
                            <input id="ForgotEmail" type="email" name="ForgotEmail" placeholder="Email" />
                            <button class="joinbutton">Send</button>
                        </form>
                
                     
                             
                    </div>
                </div>

                <!-- Modal sign up page-->
                <div id="myModal2" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div id="registerform">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3>Explore outside your circle</h3>
                        Host and discover unique and engaging events. Already a member? <a href="#myModal" role="button" class="btn" data-toggle="modal">Login </a>

                        <hr/>
                        <div class="inner">
                            <br/>
                            <div id='fb-connect' class="fb-login">
                                <div id='left'>
                                    <i class="icon-facebook icon-large"></i>
                                </div>
                                <span id='border'></span>
                                <div id='right'>Connect with Facebook</div>
                            </div>
                        
                            <h3 id='or'> or </h3>
                            
                            <div id="RegFailed"></div>
                            <br id="break"  style="display:none;"/>

                        
                            <form id="registration" autocomplete="off" method="post" action="" >
                                <div id="fullname">
                                    <input id="firstname" name="firstname" type="text" maxlength="100" placeholder="First Name" />
                                    <input id="lastname" name="lastname" type="text" maxlength="100" placeholder="Last Name" />
                                </div>
                       
                                <input id="email" name="email" type="text" maxlength="150" placeholder="Email" />   
                                <br/>
                                <input id="password" name="password" type="password" maxlength="50" value="" placeholder="Password" />
                                <input type="checkbox" class= "passcheck"/> 
                                <label for="passcheck">Show password </label>
                                <input id="college" name="college" type="text" placeholder="Where did you go to school?" />
                                <br/>
                                <label id="lbirthdate" for="birthdate">Birthday:</label>
                                <div>
                                    <div id="monthcontainer">
                                        <select type="month" id="month" name="month">
                                            <option value="" disabled="disabled" selected="selected" style="display:none;">Month</option>
                                            <option value="January">January</option>
                                            <option value="February">February</option>
                                            <option value="March">March</option>
                                            <option value="April">April</option>
                                            <option value="May">May</option>
                                            <option value="June">June</option>
                                            <option value="July">July</option>
                                            <option value="August">August</option>
                                            <option value="September">September</option>
                                            <option value="October">October</option>
                                            <option value="November">November</option>
                                            <option value="December">December</option>
                                        </select>
                                    </div>
		    
                                    <div id="daycontainer">
                                        <select type="day" id="day" name="day">
                                            <option value="" disabled="disabled" selected="selected" style="display:none;">Day</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                            <option value="6">6</option>
                                            <option value="7">7</option>
                                            <option value="8">8</option>
                                            <option value="9">9</option>
                                            <option value="10">10</option>
                                            <option value="11">11</option>
                                            <option value="12">12</option>
                                            <option value="13">13</option>
                                            <option value="14">14</option>
                                            <option value="15">15</option>
                                            <option value="16">16</option>
                                            <option value="17">17</option>
                                            <option value="18">18</option>
                                            <option value="19">19</option>
                                            <option value="20">20</option>
                                            <option value="21">21</option>
                                            <option value="22">22</option>
                                            <option value="23">23</option>
                                            <option value="24">24</option>
                                            <option value="25">25</option>
                                            <option value="26">26</option>
                                            <option value="27">27</option>
                                            <option value="28">28</option>
                                            <option value="29">29</option>
                                            <option value="30">30</option>
                                            <option value="31">31</option>
                                        </select>
		     
                                    </div>
		    
                                    <div id="yearcontainer">
                                        <!--<input tabindex="7" id="year" name="year" placeholder="year" />-->
                                        <select type="year" id="year" name="year">
                                            <option value="" disabled="disabled" selected="selected" style="display:none;">Year</option>
                                            <xsl:for-each select="years/year">
                                                <option value="{.}">
                                                    <xsl:value-of select="." />
                                                </option>
                                            </xsl:for-each>
                                        </select>
                                    </div>
                                </div>
                                <label id="lgender" for="gender">Gender:</label>
                        
                                <div id="gendercontainer">
                                    <label>
                                        <input type="radio" name="gender" value="m" /> Male</label>
                                    <label>
                                        <input type="radio" name="gender" value="f" /> Female</label>
                                </div>
                                <!--   <label id="lloc" for="location">Location:</label>
                                <div id="loccontainer">
                                    <label>
                                        <input type="hidden" name="location" value="Philadelphia" /> Philadelphia</label>
                                    <label>
                                        <input type="radio" name="location" value="Other" /> Other</label>
                                </div> -->
                                <input type="hidden" name="location" value="Philadelphia" /> 
                                <input class="joinbutton" id="signupsubmit" name="signup" type="submit" value="Join" />
                            </form>
                        </div>
                    </div>
                </div>

                
                <div class="Footer">

                    <!-- 
                    <a href="https://mixpanel.com/f/partner">
                        <img style='position: fixed; bottom: 0; left: 0' src="//cdn.mxpnl.com/site_media/images/partner/badge_light.png" alt="Mobile Analytics" />
                    </a> -->
                    
                    <div class="LeftFoot">
                        TheSocialer © 2013
                    </div>
                    <div class="RightFoot">
                        <a href="/tos">Terms of Use</a>
                        <a href="/about">About</a>
                        <a href="/privacy">Privacy</a>
                    </div>
                </div>
                <xsl:apply-templates select="JavaScript/file" />
                <xsl:if test="string-length(initJavaScript)">
                    <script type="text/JavaScript">
                        <xsl:value-of select="initJavaScript" />
                    </script>
                </xsl:if>
            </body>
        </html>
    </xsl:template>

    <xsl:template match="/Header/JavaScript/file">
        <script type="text/JavaScript">
            <xsl:attribute name="src">
                <xsl:value-of select="." />
            </xsl:attribute>
            <xsl:comment></xsl:comment>
        </script>
    </xsl:template>

    <xsl:template match="/Header/CSS/file">
        <link rel="stylesheet" type="text/css">
            <xsl:attribute name="href">
                <xsl:value-of select="." />
            </xsl:attribute>
        </link>
    </xsl:template>

    <xsl:template name="LoggedInNavigation">
        <ul class="navcontainer">
            <a href="/popups" class="NavigationLink first"> 
                <div>Discover</div>a social popup</a>
            <a href="/popups/new" class=" NavigationLink second">
                <div>Host</div>a social popup</a>
            <a href="/community" class="last NavigationLink last">
                <div>Engage</div> with people</a>

            <!--   <a href="/trending" class="second NavigationLink" >Around The City</a> -->
           
            <!--     <a href="/explore" class="second NavigationLink">Browse</a>-->
            <xsl:choose>
                <xsl:when test="./Viewer/@userId = -1">
                    <a href="#myModal" role="button" class="btn" data-toggle="modal" >Login </a>
                    <a href="#myModal2"  role="button" class="btn" data-toggle="modal" >Sign Up</a>
                </xsl:when>
                <xsl:otherwise>

                    <!--   <a href="/search" class="sixth last NavigationLink">Search</a> -->
                  
                </xsl:otherwise>
            </xsl:choose>      
        </ul>
    </xsl:template>

    <xsl:template match="FriendRequest">
        <li class="Notification">
            <xsl:attribute name="data-sender">
                <xsl:value-of select="Member/@userId" />
            </xsl:attribute>
            <img width='80px' src='/photo/{Member/@userId}/Medium'></img>
           <!-- <soc:photo size="Medium">
                <xsl:attribute name="userId">
                    <xsl:value-of select="Member/@userId" />
                </xsl:attribute>
            </soc:photo>-->
            <ol class="userDetails">
                <li class="userName">
                    <strong>
                        <xsl:value-of select="Member/@firstName" />
                        <xsl:text> </xsl:text>
                        <xsl:value-of select="Member/@lastName" />
                    </strong> 
                    wants to get social
                </li>
                <li>
                    <xsl:value-of select="Member/@Location" />
                    <xsl:text>, </xsl:text>
                    <xsl:value-of select="Member/@College" />
                </li>
                <li>
                    <button class="standard Blue accept">Accept</button>
                    <button class="standard Gray decline">Decline</button>
                </li>
            </ol>
        </li>
    </xsl:template>
  
    <xsl:template match="LocationShare">
        <li class="Notification">
            <xsl:attribute name="data-sender">
                <xsl:value-of select="Member/@userId" />
            </xsl:attribute>
            <xsl:attribute name="data-loc-id">
                <xsl:value-of select="./Location/@id" />
            </xsl:attribute>
            <xsl:attribute name="data-loc-date">
                <xsl:value-of select="./DateObject/@rawdate" />
            </xsl:attribute>
            <a href="/location/{./Location/@id}/{./@date}">
                <img border="0" style="height:70px;width:70px;" src="/Photos/Locations/{./Location/@image}" />
            </a>
            <ol class="userDetails">
                <li class="userName">
                    <a href="/profile/{Member/@userId}">
                        <strong>
                            <xsl:value-of select="Member/@firstName" />
                            <xsl:text> </xsl:text>
                            <xsl:value-of select="Member/@lastName" />
                        </strong>
                    </a> 
                    wants you to check out <a href="/location/{./Location/@id}/{./DateObject/@rawdate}">
                        <xsl:value-of select="./Location/@name" />
                    </a> on 
                    <xsl:value-of select="./DateObject/@shortDay" />, 
                    <xsl:value-of select="./DateObject/@shortMonth" />
                    <xsl:text> </xsl:text>
                    <xsl:value-of select="./DateObject/@date" />
                </li>
                <li>
                    <button class="standard Gray dismiss">Dismiss</button>
                </li>
            </ol>
        </li>
    </xsl:template>

    <xsl:template name="HowItWorks">
        <div class="HowItWorks hide">
            <div class="CloseWindow">&#10006;</div>
            <h2>Discover</h2>
            <p>
                Navigate the homepage and discover exciting things going on in your city. 
                Places will trend according to the popularity of a particular event each day. 
                Popularity is determined by a few factors, including the number of people “Going”... 
            </p>
            <br />

            <h2>Explore</h2>
            <p>
                With the “Explore Your City” tab you are now in control of your experience. 
                Find new places and locations based on your current interests. 
                Whether it be a rooftop bar, a great live music venue, or simply a park to relax in--  it’s on The Socialer.  
                Further, “liking” your favorite places allows us to curate the content most appealing to you. 
                If there is a place you love and it’s not on the site, let us know and we’ll add it!
            </p>
            <br />

            <h2>Connect</h2>
            <p>
                Connect with people who share your interests and discover more relevant experiences. 
                We find that searching based on college is a good starting point. 
                Feel free to “Get Social” with anyone who might be interesting- it’s not necessarily about getting personal, but rather sharing awesome content.
            </p>
            <br />

            <h2>Start a trend</h2>
            <p>
                Be a trendsetter by sharing and commenting about something you think is cool or exciting... 
                It’s a big world, someone is bound to agree with you. 
                Selecting the “Going” button automatically starts a trend for a particular day. 
                Oh, and don’t feel like “Going” binds you to a certain event, we aren’t taking attendance!
            </p>
            <br/>
        </div>
    </xsl:template>
</xsl:stylesheet>