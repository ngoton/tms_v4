﻿(function(f,e,b,g,c,d,h){/*! Jssor */
new(function(){});var k=f.$JssorEasing$={$EaseLinear:function(a){return a},$EaseGoBack:function(a){return 1-b.abs(2-1)},$EaseSwing:function(a){return-b.cos(a*b.PI)/2+.5},$EaseInQuad:function(a){return a*a},$EaseOutQuad:function(a){return-a*(a-2)},$EaseInOutQuad:function(a){return(a*=2)<1?1/2*a*a:-1/2*(--a*(a-2)-1)},$EaseInCubic:function(a){return a*a*a},$EaseOutCubic:function(a){return(a-=1)*a*a+1},$EaseInOutCubic:function(a){return(a*=2)<1?1/2*a*a*a:1/2*((a-=2)*a*a+2)},$EaseInQuart:function(a){return a*a*a*a},$EaseOutQuart:function(a){return-((a-=1)*a*a*a-1)},$EaseInOutQuart:function(a){return(a*=2)<1?1/2*a*a*a*a:-1/2*((a-=2)*a*a*a-2)},$EaseInQuint:function(a){return a*a*a*a*a},$EaseOutQuint:function(a){return(a-=1)*a*a*a*a+1},$EaseInOutQuint:function(a){return(a*=2)<1?1/2*a*a*a*a*a:1/2*((a-=2)*a*a*a*a+2)},$EaseInSine:function(a){return 1-b.cos(a*b.PI/2)},$EaseOutSine:function(a){return b.sin(a*b.PI/2)},$EaseInOutSine:function(a){return-1/2*(b.cos(b.PI*a)-1)},$EaseInExpo:function(a){return a==0?0:b.pow(2,10*(a-1))},$EaseOutExpo:function(a){return a==1?1:-b.pow(2,-10*a)+1},$EaseInOutExpo:function(a){return a==0||a==1?a:(a*=2)<1?1/2*b.pow(2,10*(a-1)):1/2*(-b.pow(2,-10*--a)+2)},$EaseInCirc:function(a){return-(b.sqrt(1-a*a)-1)},$EaseOutCirc:function(a){return b.sqrt(1-(a-=1)*a)},$EaseInOutCirc:function(a){return(a*=2)<1?-1/2*(b.sqrt(1-a*a)-1):1/2*(b.sqrt(1-(a-=2)*a)+1)},$EaseInElastic:function(a){if(!a||a==1)return a;var c=.3,d=.075;return-(b.pow(2,10*(a-=1))*b.sin((a-d)*2*b.PI/c))},$EaseOutElastic:function(a){if(!a||a==1)return a;var c=.3,d=.075;return b.pow(2,-10*a)*b.sin((a-d)*2*b.PI/c)+1},$EaseInOutElastic:function(a){if(!a||a==1)return a;var c=.45,d=.1125;return(a*=2)<1?-.5*b.pow(2,10*(a-=1))*b.sin((a-d)*2*b.PI/c):b.pow(2,-10*(a-=1))*b.sin((a-d)*2*b.PI/c)*.5+1},$EaseInBack:function(a){var b=1.70158;return a*a*((b+1)*a-b)},$EaseOutBack:function(a){var b=1.70158;return(a-=1)*a*((b+1)*a+b)+1},$EaseInOutBack:function(a){var b=1.70158;return(a*=2)<1?1/2*a*a*(((b*=1.525)+1)*a-b):1/2*((a-=2)*a*(((b*=1.525)+1)*a+b)+2)},$EaseInBounce:function(a){return 1-k.$EaseOutBounce(1-a)},$EaseOutBounce:function(a){return a<1/2.75?7.5625*a*a:a<2/2.75?7.5625*(a-=1.5/2.75)*a+.75:a<2.5/2.75?7.5625*(a-=2.25/2.75)*a+.9375:7.5625*(a-=2.625/2.75)*a+.984375},$EaseInOutBounce:function(a){return a<1/2?k.$EaseInBounce(a*2)*.5:k.$EaseOutBounce(a*2-1)*.5+.5},$EaseInWave:function(a){return 1-b.cos(a*b.PI*2)},$EaseOutWave:function(a){return b.sin(a*b.PI*2)},$EaseOutJump:function(a){return 1-((a*=2)<1?(a=1-a)*a*a:(a-=1)*a*a)},$EaseInJump:function(a){return(a*=2)<1?a*a*a:(a=2-a)*a*a}},p={fd:37,te:39},m,i,a=new function(){var i=this,mb=1,F=2,F=3,fb=4,jb=5,q=0,l=0,u=0,Y=0,D=0,rb=navigator.appName,k=navigator.userAgent,p=e.documentElement,B;function x(){if(!q)if(rb=="Microsoft Internet Explorer"&&!!f.attachEvent&&!!f.ActiveXObject){var d=k.indexOf("MSIE");q=mb;u=n(k.substring(d+5,k.indexOf(";",d)));/*@cc_on Y=@_jscript_version@*/;l=e.documentMode||u}else if(rb=="Netscape"&&!!f.addEventListener){var c=k.indexOf("Firefox"),a=k.indexOf("Safari"),h=k.indexOf("Chrome"),b=k.indexOf("AppleWebKit");if(c>=0){q=F;l=n(k.substring(c+8))}else if(a>=0){var i=k.substring(0,a).lastIndexOf("/");q=h>=0?fb:F;l=n(k.substring(i+1,a))}if(b>=0)D=n(k.substring(b+12))}else{var g=/(opera)(?:.*version|)[ \/]([\w.]+)/i.exec(k);if(g){q=jb;l=n(g[2])}}}function s(){x();return q==mb}function N(){return s()&&(l<6||e.compatMode=="BackCompat")}function eb(){x();return q==F}function db(){x();return q==fb}function ib(){x();return q==jb}function Z(){return eb()&&D>534&&D<535}function A(){return s()&&l<9}function v(a){if(!B){j(["transform","WebkitTransform","msTransform","MozTransform","OTransform"],function(b){if(a.style[b]!=h){B=b;return c}});B=B||"transform"}return B}function pb(a){return Object.prototype.toString.call(a)}var I;function j(a,d){if(pb(a)=="[object Array]"){for(var b=0;b<a.length;b++)if(d(a[b],b,a))return c}else for(var e in a)if(d(a[e],e,a))return c}function wb(){if(!I){I={};j(["Boolean","Number","String","Function","Array","Date","RegExp","Object"],function(a){I["[object "+a+"]"]=a.toLowerCase()})}return I}function z(a){return a==g?String(a):wb()[pb(a)]||"object"}function y(a,b){return{x:a,y:b}}function qb(b,a){setTimeout(b,a||0)}function G(b,d,c){var a=!b||b=="inherit"?"":b;j(d,function(c){var b=c.exec(a);if(b){var d=a.substr(0,b.index),e=a.substr(b.lastIndex+1,a.length-(b.lastIndex+1));a=d+e}});a=c+(a.indexOf(" ")!=0?" ":"")+a;return a}function bb(b,a){if(l<9)b.style.filter=a}function tb(b,a,c){if(Y<9){var e=b.style.filter,g=new RegExp(/[\s]*progid:DXImageTransform\.Microsoft\.Matrix\([^\)]*\)/g),f=a?"progid:DXImageTransform.Microsoft.Matrix(M11="+a[0][0]+", M12="+a[0][1]+", M21="+a[1][0]+", M22="+a[1][1]+", SizingMethod='auto expand')":"",d=G(e,[g],f);bb(b,d);i.Nc(b,c.y);i.Oc(b,c.x)}}i.Fb=s;i.Hb=db;i.Jb=A;i.H=function(){return l};i.yc=function(){x();return D};i.$Delay=qb;function nb(a){a.constructor===nb.caller&&a.dc&&a.dc()}i.dc=nb;i.M=function(a){if(i.Zd(a))a=e.getElementById(a);return a};function t(a){return a||f.event}t=t;i.Wd=function(a){a=t(a);return a.target||a.srcElement||e};i.hc=function(a){a=t(a);var b=e.body;return{x:a.pageX||a.clientX+(p.scrollLeft||b.scrollLeft||0)-(p.clientLeft||b.clientLeft||0)||0,y:a.pageY||a.clientY+(p.scrollTop||b.scrollTop||0)-(p.clientTop||b.clientTop||0)||0}};function E(c,d,a){if(a!=h)c.style[d]=a;else{var b=c.currentStyle||c.style;a=b[d];if(a==""&&f.getComputedStyle){b=c.ownerDocument.defaultView.getComputedStyle(c,g);b&&(a=b.getPropertyValue(d)||b[d])}return a}}function V(b,c,a,d){if(a!=h){d&&(a+="px");E(b,c,a)}else return n(E(b,c))}function o(d,a){var b=a&2,c=a?V:E;return function(e,a){return c(e,d,a,b)}}function ub(b){if(s()&&u<9){var a=/opacity=([^)]*)/.exec(b.style.filter||"");return a?n(a[1])/100:1}else return n(b.style.opacity||"1")}function vb(c,a,f){if(s()&&u<9){var h=c.style.filter||"",i=new RegExp(/[\s]*alpha\([^\)]*\)/g),e=b.round(100*a),d="";if(e<100||f)d="alpha(opacity="+e+") ";var g=G(h,[i],d);bb(c,g)}else c.style.opacity=a==1?"":b.round(a*100)/100}function X(e,a){var d=a.$Rotate||0,c=a.$Scale==h?1:a.$Scale;if(A()){var k=i.je(d/180*b.PI,c,c);tb(e,!d&&c==1?g:k,i.ke(k,a.$OriginalWidth,a.$OriginalHeight))}else{var f=v(e);if(f){var j="rotate("+d%360+"deg) scale("+c+")";if(db()&&D>535)j+=" perspective(2000px)";e.style[f]=j}}}i.fe=function(b,a){if(Z())qb(i.r(g,X,b,a));else X(b,a)};i.ge=function(b,c){var a=v(b);if(a)b.style[a+"Origin"]=c};i.Vd=function(a,c){if(s()&&u<9||u<10&&N())a.style.zoom=c==1?"":c;else{var b=v(a);if(b){var f="scale("+c+")",e=a.style[b],g=new RegExp(/[\s]*scale\(.*?\)/g),d=G(e,[g],f);a.style[b]=d}}};i.Kd=function(a){if(!a.style[v(a)]||a.style[v(a)]=="none")a.style[v(a)]="perspective(2000px)"};i.ib=function(b,a){return function(c){c=t(c);var e=c.type,d=c.relatedTarget||(e=="mouseout"?c.toElement:c.fromElement);(!d||d!==a&&!i.Jd(a,d))&&b(c)}};i.d=function(a,c,d,b){a=i.M(a);if(a.addEventListener){c=="mousewheel"&&a.addEventListener("DOMMouseScroll",d,b);a.addEventListener(c,d,b)}else if(a.attachEvent){a.attachEvent("on"+c,d);b&&a.setCapture&&a.setCapture()}};i.Hd=function(a,c,d,b){a=i.M(a);if(a.removeEventListener){c=="mousewheel"&&a.removeEventListener("DOMMouseScroll",d,b);a.removeEventListener(c,d,b)}else if(a.detachEvent){a.detachEvent("on"+c,d);b&&a.releaseCapture&&a.releaseCapture()}};i.Id=function(b,a){i.d(A()?e:f,"mouseup",b,a)};i.O=function(a){a=t(a);a.preventDefault&&a.preventDefault();a.cancel=c;a.returnValue=d};i.r=function(d,c){var a=[].slice.call(arguments,2),b=function(){var b=a.concat([].slice.call(arguments,0));return c.apply(d,b)};return b};i.Od=function(a,b){if(b==h)return a.textContent||a.innerText;var c=e.createTextNode(b);i.vc(a);a.appendChild(c)};i.vc=function(a){a.innerHTML=""};i.ab=function(c){for(var b=[],a=c.firstChild;a;a=a.nextSibling)a.nodeType==1&&b.push(a);return b};function ob(a,c,e,b){b=b||"u";for(a=a?a.firstChild:g;a;a=a.nextSibling)if(a.nodeType==1){if(R(a,b)==c)return a;if(!e){var d=ob(a,c,e,b);if(d)return d}}}i.o=ob;function gb(a,c,d){for(a=a?a.firstChild:g;a;a=a.nextSibling)if(a.nodeType==1){if(a.tagName==c)return a;if(!d){var b=gb(a,c,d);if(b)return b}}}i.ve=gb;i.xe=function(b,a){return b.getElementsByTagName(a)};function U(c){for(var b=1;b<arguments.length;b++){var a=arguments[b];if(a)for(var d in a)c[d]=a[d]}return c}i.t=U;i.ze=function(a){return z(a)=="function"};i.Zd=function(a){return z(a)=="string"};i.re=function(a){return!isNaN(n(a))&&isFinite(a)};i.f=j;function P(a){return e.createElement(a)}i.N=function(){return P("DIV",e)};i.Ae=function(){return P("SPAN",e)};i.Ab=function(){};function S(b,c,a){if(a==h)return b.getAttribute(c);b.setAttribute(c,a)}function R(a,b){return S(a,b)||S(a,"data-"+b)}i.eb=R;function r(b,a){if(a==h)return b.className;b.className=a}i.oc=r;i.qc=function(a){return a.parentNode};i.D=function(a){i.F(a,"none")};i.q=function(a,b){i.F(a,b?"none":"")};i.dd=function(b,a){b.removeAttribute(a)};i.ed=function(){return s()&&l<10};i.Yc=function(d,c){if(c)d.style.clip="rect("+b.round(c.$Top)+"px "+b.round(c.$Right)+"px "+b.round(c.$Bottom)+"px "+b.round(c.$Left)+"px)";else{var g=d.style.cssText,f=[new RegExp(/[\s]*clip: rect\(.*?\)[;]?/i),new RegExp(/[\s]*cliptop: .*?[;]?/i),new RegExp(/[\s]*clipright: .*?[;]?/i),new RegExp(/[\s]*clipbottom: .*?[;]?/i),new RegExp(/[\s]*clipleft: .*?[;]?/i)],e=G(g,f,"");a.ob(d,e)}};i.z=function(){return+new Date};i.v=function(b,a){b.appendChild(a)};i.jb=function(c,b,a){c.insertBefore(b,a)};i.W=function(b,a){b.removeChild(a)};i.Wc=function(b,a){j(a,function(a){i.W(b,a)})};i.Xc=function(a){i.Wc(a,i.ab(a))};function n(a){return parseFloat(a)}i.Jd=function(b,a){var c=e.body;while(a&&b!=a&&c!=a)try{a=a.parentNode}catch(f){return d}return b==a};function T(b,a){return b.cloneNode(!a)}i.V=T;function M(a){if(a){var b=a.$FlyDirection;if(b&1)a.x=a.$ScaleHorizontal||1;if(b&2)a.x=-a.$ScaleHorizontal||-1;if(b&4)a.y=a.$ScaleVertical||1;if(b&8)a.y=-a.$ScaleVertical||-1;if(a.$Rotate==c)a.$Rotate=1;M(a.$Brother)}}i.kb=function(a){if(a){for(var b=0;b<a.length;b++)M(a[b]);for(var c in a)M(a[c])}};function O(b,a,c){a.onload=g;a.onerror=a.onabort=g;b&&b(a,c)}i.X=function(e,b){if(ib()&&l<11.6||!e)O(b,g,!e);else{var a=new Image;a.onload=i.r(g,O,b,a,d);a.onerror=a.onabort=i.r(g,O,b,a,c);a.src=e}};i.td=function(e,b,f){var d=e.length+1;function c(a){d--;if(b&&a&&a.src==b.src)b=a;!d&&f&&f(b)}j(e,function(b){a.X(b.src,c)});c()};i.ic=function(c,j,i,h){if(h)c=T(c);for(var g=a.xe(c,j),e=g.length-1;e>-1;e--){var b=g[e],d=T(i);r(d,r(b));a.ob(d,b.style.cssText);var f=a.qc(b);a.jb(f,d,b);a.W(f,b)}return c};var C;function yb(b){var g=this,m,k,l,e;function f(){var a=m;if(e)a+="ds";else if(k)a+="dn";else if(l)a+="av";r(b,a)}function n(a){if(e)i.O(a);else{C.push(g);k=c;f()}}g.yd=function(){k=d;f()};g.cc=function(a){if(a!=h){l=a;f()}else return l};g.$Enable=function(a){if(a!=h){e=!a;f()}else return!e};b=i.M(b);if(!C){i.Id(function(){var a=C;C=[];j(a,function(a){a.yd()})});C=[]}m=r(b);a.d(b,"mousedown",n)}i.ub=function(a){return new yb(a)};i.rb=E;i.U=o("overflow");i.l=o("top",2);i.m=o("left",2);i.i=o("width",2);i.g=o("height",2);i.Oc=o("marginLeft",2);i.Nc=o("marginTop",2);i.u=o("position");i.F=o("display");i.B=o("zIndex",1);i.Rb=function(b,a,c){if(a!=h)vb(b,a,c);else return ub(b)};i.ob=function(a,b){if(b!=h)a.style.cssText=b;else return a.style.cssText};var Q={$Opacity:i.Rb,$Top:i.l,$Left:i.m,Qb:i.i,zb:i.g,T:i.u,Xf:i.F,$ZIndex:i.B},w;function H(){if(!w)w=U({Wf:i.Nc,Vf:i.Oc,$Clip:i.Yc,hb:i.fe},Q);return w}i.kd=H;i.I=function(c,b){var a=H();j(b,function(d,b){a[b]&&a[b](c,d)})};m=new function(){var a=this;function b(d,g){for(var j=d[0].length,i=d.length,h=g[0].length,f=[],c=0;c<i;c++)for(var k=f[c]=[],b=0;b<h;b++){for(var e=0,a=0;a<j;a++)e+=d[c][a]*g[a][b];k[b]=e}return f}a.vb=function(d,c){var a=b(d,[[c.x],[c.y]]);return y(a[0][0],a[1][0])}};i.je=function(d,a,c){var e=b.cos(d),f=b.sin(d);return[[e*a,-f*c],[f*a,e*c]]};i.ke=function(d,c,a){var e=m.vb(d,y(-c/2,-a/2)),f=m.vb(d,y(c/2,-a/2)),g=m.vb(d,y(c/2,a/2)),h=m.vb(d,y(-c/2,a/2));return y(b.min(e.x,f.x,g.x,h.x)+c/2,b.min(e.y,f.y,g.y,h.y)+a/2)};i.hb=function(j,k,t,q,u,w,h){var c=k;if(j){c={};for(var e in k){var x=w[e]||1,r=u[e]||[0,1],d=(t-r[0])/r[1];d=b.min(b.max(d,0),1);d=d*x;var o=b.floor(d);if(d!=o)d-=o;var v=q[e]||q.wb,p=v(d),f,s=j[e],n=k[e];if(a.re(n))f=s+(n-s)*p;else{f=a.t({L:{}},j[e]);a.f(n.L,function(c,b){var a=c*p;f.L[b]=a;f[b]+=a})}c[e]=f}if(j.$Zoom)c.hb={$Rotate:c.$Rotate||0,$Scale:c.$Zoom,$OriginalWidth:h.$OriginalWidth,$OriginalHeight:h.$OriginalHeight}}if(k.$Clip&&h.$Move){var i=c.$Clip.L,m=(i.$Top||0)+(i.$Bottom||0),l=(i.$Left||0)+(i.$Right||0);c.$Left=(c.$Left||0)+l;c.$Top=(c.$Top||0)+m;c.$Clip.$Left-=l;c.$Clip.$Right-=l;c.$Clip.$Top-=m;c.$Clip.$Bottom-=m}if(c.$Clip&&a.ed()&&!c.$Clip.$Top&&!c.$Clip.$Left&&c.$Clip.$Right==h.$OriginalWidth&&c.$Clip.$Bottom==h.$OriginalHeight)c.$Clip=g;return c}},l=function(){var b=this,d=[];function i(a,b){d.push({Yb:a,Bb:b})}function h(b,c){a.f(d,function(a,e){a.Yb==b&&a.Bb===c&&d.splice(e,1)})}b.$On=b.addEventListener=i;b.$Off=b.removeEventListener=h;b.b=function(b){var c=[].slice.call(arguments,1);a.f(d,function(a){try{a.Yb==b&&a.Bb.apply(f,c)}catch(d){}})}};i=function(m,x,j,P,N,J){m=m||0;var e=this,q,n,o,w,y=0,G,H,F,A,i=0,s=0,B,l=m,h,g,p,t=[],z;function K(b){h+=b;g+=b;l+=b;i+=b;s+=b;a.f(t,function(a){a,a.$Shift(b)})}function O(a,b){var c=a-h+m*b;K(c);return g}function v(k,n){var d=k;if(p&&(d>=g||d<=h))d=((d-h)%p+p)%p+h;if(!B||w||n||i!=d){var f=b.min(d,g);f=b.max(f,h);if(!B||w||n||f!=s){if(J){var m=(f-l)/(x||1);if(j.$Reverse)m=1-m;var o=a.hb(N,J,m,G,F,H,j);a.f(o,function(b,a){z[a]&&z[a](P,b)})}e.Cb(s-l,f-l)}s=f;a.f(t,function(b,c){var a=k<i?t[t.length-c-1]:b;a.A(k,n)});var r=i,q=k;i=d;B=c;e.tb(r,q)}}function C(a,c){c&&a.lc(g,1);g=b.max(g,a.Y());t.push(a)}var D=f.requestAnimationFrame||f.webkitRequestAnimationFrame||f.mozRequestAnimationFrame||f.msRequestAnimationFrame||function(b){a.$Delay(b,j.$Interval)};function I(){if(q){var d=a.z(),e=b.min(d-y,100),c=i+e*o;y=d;if(c*o>=n*o)c=n;v(c);if(!w&&c*o>=n*o)L(A);else D(I)}}function u(d,f,j){if(!q){q=c;w=j;A=f;d=b.max(d,h);d=b.min(d,g);n=d;o=n<i?-1:1;e.wc();y=a.z();D(I)}}function L(a){if(q){w=q=A=d;e.xc();a&&a()}}e.$Play=function(a,b,c){u(a?i+a:g,b,c)};e.sc=u;e.S=L;e.rd=function(a){u(a)};e.C=function(){return i};e.tc=function(){return n};e.bb=function(){return s};e.A=v;e.jc=function(){v(h,c)};e.$Move=function(a){v(i+a)};e.$IsPlaying=function(){return q};e.Tc=function(a){p=a};e.lc=O;e.$Shift=K;e.Lb=function(a){C(a,0)};e.Ib=function(a){C(a,1)};e.Y=function(){return g};e.tb=e.wc=e.xc=e.Cb=a.Ab;e.Gb=a.z();j=a.t({$Interval:15},j);p=j.ec;z=a.t({},a.kd(),j.rc);h=l=m;g=m+x;H=j.$Round||{};F=j.$During||{};G=a.t({wb:a.ze(j.$Easing)&&j.$Easing||k.$EaseSwing},j.$Easing)};var q;new function(){;function m(q,kc){var j=this;function Fc(){var a=this;i.call(a,-1e8,2e8);a.me=function(){var c=a.bb(),d=b.floor(c),f=u(d),e=c-b.floor(c);return{J:f,ne:d,T:e}};a.tb=function(d,a){var e=b.floor(a);if(e!=a&&a>d)e++;Xb(e,c);j.b(m.$EVT_POSITION_CHANGE,u(a),u(d),a,d)}}function Ec(){var b=this;i.call(b,0,0,{ec:t});a.f(D,function(a){M&1&&a.Tc(t);b.Ib(a);a.$Shift(lb/ec)})}function Dc(){var a=this,b=Wb.$Elmt;i.call(a,-1,2,{$Easing:k.$EaseLinear,rc:{T:cc},ec:t},b,{T:1},{T:-1});a.qb=b}function sc(n,l){var a=this,e,f,h,k,b;i.call(a,-1e8,2e8);a.wc=function(){T=c;Y=g;j.b(m.$EVT_SWIPE_START,u(y.C()),y.C())};a.xc=function(){T=d;k=d;var a=y.me();j.b(m.$EVT_SWIPE_END,u(y.C()),y.C());!a.T&&Hc(a.ne,s)};a.tb=function(g,d){var a;if(k)a=b;else{a=f;if(h){var c=d/h;a=o.$SlideEasing(c)*(f-e)+e}}y.A(a)};a.pb=function(b,d,c,g){e=b;f=d;h=c;y.A(b);a.A(0);a.sc(c,g)};a.Sd=function(d){k=c;b=d;a.$Play(d,g,c)};a.Uc=function(a){b=a};y=new Fc;y.Lb(n);y.Lb(l)}function tc(){var c=this,b=bc();a.B(b,0);c.$Elmt=b;c.nb=function(){a.D(b);a.vc(b)}}function Cc(p,n){var e=this,r,x,H,y,f,A=[],R,q,T,G,P,F,h,w,k;i.call(e,-v,v+1,{});function E(a){x&&x.Mb();r&&r.Mb();S(p,a);F=c;r=new I.$Class(p,I,1);x=new I.$Class(p,I);x.jc();r.jc()}function Z(){r.Gb<I.Gb&&E()}function M(n,q,l){if(!G){G=c;if(f&&l){var g=l.width,b=l.height,k=g,i=b;if(g&&b&&o.$FillMode){if(o.$FillMode&3&&(!(o.$FillMode&4)||g>L||b>K)){var h=d,p=L/K*b/g;if(o.$FillMode&1)h=p>1;else if(o.$FillMode&2)h=p<1;k=h?g*K/b:L;i=h?K:b*L/g}a.i(f,k);a.g(f,i);a.l(f,(K-i)/2);a.m(f,(L-k)/2)}a.u(f,"absolute");j.b(m.$EVT_LOAD_END,hc)}}a.D(q);n&&n(e)}function X(b,c,d,f){if(f==Y&&s==n&&U)if(!Gc){var a=u(b);B.oe(a,n,c,e,d);c.be();fb.lc(a,1);fb.A(a);z.pb(b,b,0)}}function ab(b){if(b==Y&&s==n){if(!h){var a=g;if(B)if(B.J==n)a=B.pe();else B.nb();Z();h=new Ac(n,a,e.ae(),e.ce());h.Mc(k)}!h.$IsPlaying()&&h.Eb()}}function Q(d,c){if(d==n){if(d!=c)D[c]&&D[c].Yd();else h&&h.de();k&&k.$Enable();var j=Y=a.z();e.X(a.r(g,ab,j))}else{var i=b.abs(n-d),f=v+o.$LazyLoading;(!P||i<=f||t-i<=f)&&e.X()}}function bb(){if(s==n&&h){h.S();k&&k.$Quit();k&&k.$Disable();h.Dc()}}function cb(){s==n&&h&&h.S()}function O(b){if(W)a.O(b);else j.b(m.$EVT_CLICK,n,b)}function N(){k=w.pInstance;h&&h.Mc(k)}e.X=function(d,b){b=b||y;if(A.length&&!G){a.q(b);if(!T){T=c;j.b(m.$EVT_LOAD_START);a.f(A,function(b){if(!b.src){b.src=a.eb(b,"src2");a.F(b,b["display-origin"])}})}a.td(A,f,a.r(g,M,d,b))}else M(d,b)};e.Fd=function(){if(B){var b=B.ad(t);if(b){var e=Y=a.z(),c=n+ac,d=D[u(c)];return d.X(a.r(g,X,c,d,b,e),y)}}gb(s+o.$AutoPlaySteps*ac)};e.yb=function(){Q(n,n)};e.Yd=function(){k&&k.$Quit();k&&k.$Disable();e.Jc();h&&h.Td();h=g;E()};e.be=function(){a.D(p)};e.Jc=function(){a.q(p)};e.Ud=function(){k&&k.$Enable()};function S(b,e,d){if(b["jssor-slider"])return;d=d||0;if(!F){if(b.tagName=="IMG"){A.push(b);if(!b.src){P=c;b["display-origin"]=a.F(b);a.D(b)}}a.Jb()&&a.B(b,(a.B(b)||0)+1);if(o.$HWA&&a.yc())(!J||a.yc()<534||!jb&&!a.Hb())&&a.Kd(b)}var g=a.ab(b);a.f(g,function(g){var i=a.eb(g,"u");if(i=="player"&&!w){w=g;if(w.pInstance)N();else a.d(w,"dataavailable",N)}if(i=="caption"){if(!a.Fb()&&!e){var h=a.V(g);a.jb(b,h,g);a.W(b,g);g=h;e=c}}else if(!F&&!d&&!f&&a.eb(g,"u")=="image"){f=g;if(f){if(f.tagName=="A"){R=f;a.I(R,V);q=a.V(f,c);a.d(q,"click",O);a.I(q,V);a.F(q,"block");a.Rb(q,0);a.rb(q,"backgroundColor","#000");f=a.ve(f,"IMG")}f.border=0;a.I(f,V)}}S(g,e,d+1)})}e.Cb=function(c,b){var a=v-b;cc(H,a)};e.ae=function(){return r};e.ce=function(){return x};e.J=n;l.call(e);var C=a.o(p,"thumb",c);if(C){e.ye=a.V(C);a.dd(C,"id");a.D(C)}a.q(p);y=a.V(ib);a.B(y,1e3);a.d(p,"click",O);E(c);e.kc=f;e.Lc=q;e.qb=H=p;a.v(H,y);j.$On(203,Q);j.$On(28,cb);j.$On(24,bb)}function Ac(h,q,v,u){var b=this,l=0,x=0,n,g,e,f,k,r,w,t,p=D[h];i.call(b,0,0);function y(){a.Xc(P);ic&&k&&p.Lc&&a.v(P,p.Lc);a.q(P,!k&&p.kc)}function z(){if(r){r=d;j.b(m.$EVT_ROLLBACK_END,h,e,l,g,e,f);b.A(g)}b.Eb()}function A(a){t=a;b.S();b.Eb()}b.Eb=function(){var a=b.bb();if(!C&&!T&&!t&&s==h){if(!a){if(n&&!k){k=c;b.Dc(c);j.b(m.$EVT_SLIDESHOW_START,h,l,x,n,f)}y()}var d,o=m.$EVT_STATE_CHANGE;if(a!=f)if(a==e)d=f;else if(a==g)d=e;else if(!a)d=g;else if(a>e){r=c;d=e;o=m.$EVT_ROLLBACK_START}else d=b.tc();j.b(o,h,a,l,g,e,f);var i=U&&(!Q||G);if(a==f)i&&p.Fd();else(i||a!=e)&&b.sc(d,z)}};b.de=function(){e==f&&e==b.bb()&&b.A(g)};b.Td=function(){B&&B.J==h&&B.nb();var a=b.bb();a<f&&j.b(m.$EVT_STATE_CHANGE,h,-a-1,l,g,e,f)};b.Dc=function(b){q&&a.U(nb,b&&q.pc.$Outside?"":"hidden")};b.Cb=function(b,a){if(k&&a>=n){k=d;y();p.Jc();B.nb();j.b(m.$EVT_SLIDESHOW_END,h,l,x,n,f)}j.b(m.$EVT_PROGRESS_CHANGE,h,a,l,g,e,f)};b.Mc=function(a){if(a&&!w){w=a;a.$On($JssorPlayer$.Dd,A)}};q&&b.Ib(q);n=b.Y();b.Y();b.Ib(v);g=v.Y();e=g+o.$AutoPlayInterval;u.$Shift(e);b.Lb(u);f=b.Y()}function cc(e,g){var f=x>0?x:mb,c=Eb*g*(f&1),d=Fb*g*(f>>1&1);if(a.Hb()&&a.H()<38){c=c.toFixed(3);d=d.toFixed(3)}else{c=b.round(c);d=b.round(d)}if(a.Fb()&&a.H()>=10&&a.H()<11)e.style.msTransform="translate("+c+"px, "+d+"px)";else if(a.Hb()&&a.H()>=30&&a.H()<34){e.style.WebkitTransition="transform 0s";e.style.WebkitTransform="translate3d("+c+"px, "+d+"px, 0px) perspective(2000px)"}else{a.m(e,c);a.l(e,d)}}function yc(c){var b=a.Wd(c).tagName;!N&&b!="INPUT"&&b!="TEXTAREA"&&b!="SELECT"&&wc()&&xc(c)}function Lb(){ub=T;Pb=z.tc();E=y.C();if(C||!G&&Q&12){z.S();j.b(m.cd)}}function jc(e){if(!C&&(G||!(Q&12))&&!z.$IsPlaying()){var c=y.C(),a=b.ceil(E);if(e&&b.abs(F)>=o.$MinDragOffsetToSlide){a=b.ceil(c);a+=kb}if(!(M&1))a=b.min(t-v,b.max(a,0));var d=b.abs(a-c);d=1-b.pow(1-d,5);if(!W&&ub)z.rd(Pb);else if(c==a){xb.Ud();xb.yb()}else z.pb(c,a,d*Yb)}}function xc(b){C=c;Db=d;Y=g;a.d(e,sb,fc);a.z();W=0;Lb();if(!ub)x=0;if(J){var h=b.touches[0];yb=h.clientX;zb=h.clientY}else{var f=a.hc(b);yb=f.x;zb=f.y;a.O(b)}F=0;hb=0;kb=0;j.b(m.$EVT_DRAG_START,u(E),E,b)}function fc(e){if(C&&(!a.Jb()||e.button)){var f;if(J){var l=e.touches;if(l&&l.length>0)f={x:l[0].clientX,y:l[0].clientY}}else f=a.hc(e);if(f){var j=f.x-yb,k=f.y-zb;if(b.floor(E)!=E)x=x||mb&N;if((j||k)&&!x){if(N==3)if(b.abs(k)>b.abs(j))x=2;else x=1;else x=N;if(J&&x==1&&b.abs(k)-b.abs(j)>3)Db=c}if(x){var d=k,i=Fb;if(x==1){d=j;i=Eb}if(!(M&1)){if(d>0){var g=i*s,h=d-g;if(h>0)d=g+b.sqrt(h)*5}if(d<0){var g=i*(t-v-s),h=-d-g;if(h>0)d=-g-b.sqrt(h)*5}}if(F-hb<-2)kb=0;else if(F-hb>2)kb=-1;hb=F;F=d;wb=E-F/i/(eb||1);if(F&&x&&!Db){a.O(e);if(!T)z.Sd(wb);else z.Uc(wb)}else a.Jb()&&a.O(e)}}}else Ib(e)}function Ib(f){uc();if(C){C=d;a.z();a.Hd(e,sb,fc);W=F;W&&a.O(f);z.S();var b=y.C();j.b(m.$EVT_DRAG_END,u(b),b,u(E),E,f);jc(c);Lb()}}function rc(a){D[s];s=u(a);xb=D[s];Xb(a);return s}function Hc(a,b){x=0;rc(a);j.b(m.$EVT_PARK,u(a),b)}function Xb(b,c){Bb=b;a.f(S,function(a){a.Ob(u(b),b,c)})}function wc(){var b=m.Fc||0,a=R;if(J)a&1&&(a&=1);m.Fc|=a;return N=a&~b}function uc(){if(N){m.Fc&=~R;N=0}}function bc(){var b=a.N();a.I(b,V);a.u(b,"absolute");return b}function u(a){return(a%t+t)%t}function oc(a,c){if(c)if(!M){a=b.min(b.max(a+Bb,0),t-v);c=d}else if(M&2){a=u(a+Bb);c=d}gb(a,o.$SlideDuration,c)}function Cb(){a.f(S,function(a){a.Zb(a.lb.$ChanceToShow<=G)})}function mc(){if(!G){G=1;Cb();if(!C){Q&12&&jc();Q&3&&D[s].yb()}}}function lc(){if(G){G=0;Cb();C||!(Q&12)||Lb()}}function nc(){V={Qb:L,zb:K,$Top:0,$Left:0};a.f(Z,function(b){a.I(b,V);a.u(b,"absolute");a.U(b,"hidden");a.D(b)});a.I(ib,V)}function qb(b,a){gb(b,a,c)}function gb(g,f,k){if(Ub&&(!C||o.$NaviQuitDrag)){T=c;C=d;z.S();if(f==h)f=Yb;var e=Jb.bb(),a=g;if(k){a=e+g;if(g>0)a=b.ceil(a);else a=b.floor(a)}if(!(M&1)){a=u(a);a=b.max(0,b.min(a,t-v))}var j=(a-e)%t;a=e+j;var i=e==a?0:f*b.abs(j);i=b.min(i,f*v*1.5);z.pb(e,a,i||1)}}j.$PlayTo=gb;j.$GoTo=function(a){gb(a,1)};j.$Next=function(){qb(1)};j.$Prev=function(){qb(-1)};j.$Pause=function(){U=d};j.$Play=function(){if(!U){U=c;D[s]&&D[s].yb()}};j.$SetSlideshowTransitions=function(b){a.kb(b);o.$SlideshowOptions.$Transitions=b};j.$SetCaptionTransitions=function(b){a.kb(b);I.ud=b;I.Gb=a.z()};j.$SlidesCount=function(){return Z.length};j.$CurrentIndex=function(){return s};j.$IsAutoPlaying=function(){return U};j.$IsDragging=function(){return C};j.$IsSliding=function(){return T};j.$IsMouseOver=function(){return!G};j.$LastDragSucceded=function(){return W};function db(){return a.i(w||q)}function ob(){return a.g(w||q)}j.$OriginalWidth=j.$GetOriginalWidth=db;j.$OriginalHeight=j.$GetOriginalHeight=ob;function Mb(c,f){if(c==h)return a.i(q);if(!w){var b=a.N(e);a.ob(b,a.ob(q));a.oc(b,a.oc(q));a.u(b,"relative");a.l(b,0);a.m(b,0);a.U(b,"visible");w=a.N(e);a.u(w,"absolute");a.l(w,0);a.m(w,0);a.i(w,a.i(q));a.g(w,a.g(q));a.ge(w,"0 0");a.v(w,b);var i=a.ab(q);a.v(q,w);a.rb(q,"backgroundImage","");var g={navigator:bb&&bb.$Scale==d,arrowleft:O&&O.$Scale==d,arrowright:O&&O.$Scale==d,thumbnavigator:H&&H.$Scale==d,thumbwrapper:H&&H.$Scale==d};a.f(i,function(c){a.v(g[a.eb(c,"u")]?q:b,c)});a.q(b);a.q(w)}eb=c/(f?a.g:a.i)(w);a.Vd(w,eb);a.i(q,f?eb*db():c);a.g(q,f?c:eb*ob());a.f(S,function(a){a.Wb()})}j.$ScaleHeight=j.$GetScaleHeight=function(b){if(b==h)return a.g(q);Mb(b,c)};j.$ScaleWidth=j.$SetScaleWidth=j.$GetScaleWidth=Mb;j.Kc=function(a){var d=b.ceil(u(lb/ec)),c=u(a-s+d);if(c>v){if(a-s>t/2)a-=t;else if(a-s<=-t/2)a+=t}else a=s+c-d;return a};l.call(this);j.$Elmt=q=a.M(q);var o=a.t({$FillMode:0,$LazyLoading:1,$StartIndex:0,$AutoPlay:d,$Loop:1,$HWA:c,$NaviQuitDrag:c,$AutoPlaySteps:1,$AutoPlayInterval:3e3,$PauseOnHover:1,$SlideDuration:500,$SlideEasing:k.$EaseOutQuad,$MinDragOffsetToSlide:20,$SlideSpacing:0,$DisplayPieces:1,$ParkingPosition:0,$UISearchMode:1,$PlayOrientation:1,$DragOrientation:1},kc),mb=o.$PlayOrientation&3,ac=(o.$PlayOrientation&4)/-4||1,cb=o.$SlideshowOptions,I=a.t({$Class:r},o.Fe);a.kb(I.ud);var bb=o.$BulletNavigatorOptions,O=o.$ArrowNavigatorOptions,H=o.$ThumbnailNavigatorOptions,X=!o.$UISearchMode,w,A=a.o(q,"slides",X),ib=a.o(q,"loading",X)||a.N(e),Ob=a.o(q,"navigator",X),gc=a.o(q,"arrowleft",X),dc=a.o(q,"arrowright",X),Nb=a.o(q,"thumbnavigator",X),qc=a.i(A),pc=a.g(A),V,Z=[],zc=a.ab(A);a.f(zc,function(b){b.tagName=="DIV"&&!a.eb(b,"u")&&Z.push(b)});var s=-1,Bb,xb,t=Z.length,L=o.$SlideWidth||qc,K=o.$SlideHeight||pc,Zb=o.$SlideSpacing,Eb=L+Zb,Fb=K+Zb,ec=mb&1?Eb:Fb,v=b.min(o.$DisplayPieces,t),nb,x,N,Db,J,S=[],Tb,Vb,Sb,ic,Gc,U,Q=o.$PauseOnHover,Yb=o.$SlideDuration,vb,jb,lb,Ub=v<t,M=Ub?o.$Loop:0,R,W,G=1,T,C,Y,yb=0,zb=0,F,hb,kb,Jb,y,fb,z,Wb=new tc,eb;U=o.$AutoPlay;j.lb=kc;nc();q["jssor-slider"]=c;a.B(A,a.B(A)||0);a.u(A,"absolute");nb=a.V(A);a.jb(a.qc(A),nb,A);if(cb){ic=cb.$ShowLink;vb=cb.$Class;a.kb(cb.$Transitions);jb=v==1&&t>1&&vb&&(!a.Fb()||a.H()>=8)}lb=jb||v>=t||!(M&1)?0:o.$ParkingPosition;R=(v>1||lb?mb:-1)&o.$DragOrientation;var Ab=A,D=[],B,P,Hb="mousedown",sb="mousemove",Kb="mouseup",rb,E,ub,Pb,wb,ab;if(f.navigator.pointerEnabled||(ab=f.navigator.msPointerEnabled)){Hb=ab?"MSPointerDown":"pointerdown";sb=ab?"MSPointerMove":"pointermove";Kb=ab?"MSPointerUp":"pointerup";rb=ab?"MSPointerCancel":"pointercancel";if(R){var Gb="none";if(R==1)Gb="pan-y";else if(R==2)Gb="pan-x";a.rb(Ab,ab?"msTouchAction":"touchAction",Gb)}}else if("ontouchstart"in f||"createTouch"in e){J=c;Hb="touchstart";sb="touchmove";Kb="touchend";rb="touchcancel"}fb=new Dc;if(jb)B=new vb(Wb,L,K,cb,J);a.v(nb,fb.qb);a.U(A,"hidden");P=bc();a.rb(P,"backgroundColor","#000");a.Rb(P,0);a.jb(Ab,P,Ab.firstChild);for(var tb=0;tb<Z.length;tb++){var Bc=Z[tb],hc=new Cc(Bc,tb);D.push(hc)}a.D(ib);Jb=new Ec;z=new sc(Jb,fb);if(R){a.d(A,Hb,yc);a.d(e,Kb,Ib);rb&&a.d(e,rb,Ib)}Q&=J?10:5;if(Ob&&bb){Tb=new bb.$Class(Ob,bb,db(),ob());S.push(Tb)}if(O&&gc&&dc){Vb=new O.$Class(gc,dc,O,db(),ob());S.push(Vb)}if(Nb&&H){H.$StartIndex=o.$StartIndex;Sb=new H.$Class(Nb,H);S.push(Sb)}a.f(S,function(a){a.Vb(t,D,ib);a.$On(n.sb,oc)});Mb(db());a.d(q,"mouseout",a.ib(mc,q));a.d(q,"mouseover",a.ib(lc,q));Cb();o.$ArrowKeyNavigation&&a.d(e,"keydown",function(a){if(a.keyCode==p.fd)qb(-1);else a.keyCode==p.te&&qb(1)});var pb=o.$StartIndex;if(!(M&1))pb=b.max(0,b.min(pb,t-v));z.pb(pb,pb,0)}m.$EVT_CLICK=21;m.$EVT_DRAG_START=22;m.$EVT_DRAG_END=23;m.$EVT_SWIPE_START=24;m.$EVT_SWIPE_END=25;m.$EVT_LOAD_START=26;m.$EVT_LOAD_END=27;m.cd=28;m.$EVT_POSITION_CHANGE=202;m.$EVT_PARK=203;m.$EVT_SLIDESHOW_START=206;m.$EVT_SLIDESHOW_END=207;m.$EVT_PROGRESS_CHANGE=208;m.$EVT_STATE_CHANGE=209;m.$EVT_ROLLBACK_START=210;m.$EVT_ROLLBACK_END=211;f.$JssorSlider$=q=m};var n={sb:1};f.$JssorBulletNavigator$=function(f,F,E,C){var h=this;l.call(h);f=a.M(f);var t,u,s,r,m=0,e,o,k,y,z,j,i,q,p,D=[],A=[];function x(a){a!=-1&&A[a].cc(a==m)}function v(a){h.b(n.sb,a*o)}h.$Elmt=f;h.Ob=function(a){if(a!=r){var d=m,c=b.floor(a/o);m=c;r=a;x(d);x(c)}};h.Zb=function(b){a.q(f,b)};var B;h.Wb=function(){if(!B||e.$Scale==d){e.$AutoCenter&1&&a.m(f,(E-u)/2);e.$AutoCenter&2&&a.l(f,(C-s)/2);B=c}};var w;h.Vb=function(C){if(!w){t=b.ceil(C/o);m=0;var n=q+y,r=p+z,l=b.ceil(t/k)-1;u=q+n*(!j?l:k-1);s=p+r*(j?l:k-1);a.i(f,u);a.g(f,s);for(var d=0;d<t;d++){var B=a.Ae();a.Od(B,d+1);var h=a.ic(i,"NumberTemplate",B,c);a.u(h,"absolute");var x=d%(l+1);a.m(h,!j?n*x:d%k*n);a.l(h,j?r*x:b.floor(d/(l+1))*r);a.v(f,h);D[d]=h;e.$ActionMode&1&&a.d(h,"click",a.r(g,v,d));e.$ActionMode&2&&a.d(h,"mouseover",a.ib(a.r(g,v,d),h));A[d]=a.ub(h)}w=c}};h.lb=e=a.t({$SpacingX:0,$SpacingY:0,$Orientation:1,$ActionMode:1},F);i=a.o(f,"prototype");q=a.i(i);p=a.g(i);a.W(f,i);o=e.$Steps||1;k=e.$Lanes||1;y=e.$SpacingX;z=e.$SpacingY;j=e.$Orientation-1};f.$JssorArrowNavigator$=function(e,f,t,o,m){var b=this;l.call(b);var h,j,r=a.i(e),p=a.g(e);function k(a){b.b(n.sb,a,c)}b.Ob=function(b,a,c){if(c);};b.Zb=function(b){a.q(e,b);a.q(f,b)};var s;b.Wb=function(){if(!s||h.$Scale==d){if(h.$AutoCenter&1){a.m(e,(o-r)/2);a.m(f,(o-r)/2)}if(h.$AutoCenter&2){a.l(e,(m-p)/2);a.l(f,(m-p)/2)}s=c}};var q;b.Vb=function(b){if(!q){a.d(e,"click",a.r(g,k,-j));a.d(f,"click",a.r(g,k,j));a.ub(e);a.ub(f);q=c}};b.lb=h=a.t({$Steps:1},t);j=h.$Steps};f.$JssorThumbnailNavigator$=function(i,A){var h=this,x,m,e,u=[],y,w,f,o,p,t,s,k,r,g,j;l.call(h);i=a.M(i);function z(o,d){var g=this,b,l,k;function p(){l.cc(m==d)}function i(){if(!r.$LastDragSucceded()){var a=f-d%f,b=r.Kc((d+a)/f-1),c=b*f+f-a;h.b(n.sb,c)}}g.J=d;g.Qc=p;k=o.ye||o.kc||a.N();g.qb=b=a.ic(j,"ThumbnailTemplate",k,c);l=a.ub(b);e.$ActionMode&1&&a.d(b,"click",i);e.$ActionMode&2&&a.d(b,"mouseover",a.ib(i,b))}h.Ob=function(c,d,e){var a=m;m=c;a!=-1&&u[a].Qc();u[c].Qc();!e&&r.$PlayTo(r.Kc(b.floor(d/f)))};h.Zb=function(b){a.q(i,b)};h.Wb=a.Ab;var v;h.Vb=function(F,D){if(!v){x=F;b.ceil(x/f);m=-1;k=b.min(k,D.length);var h=e.$Orientation&1,n=t+(t+o)*(f-1)*(1-h),l=s+(s+p)*(f-1)*h,C=n+(n+o)*(k-1)*h,A=l+(l+p)*(k-1)*(1-h);a.u(g,"absolute");a.U(g,"hidden");e.$AutoCenter&1&&a.m(g,(y-C)/2);e.$AutoCenter&2&&a.l(g,(w-A)/2);a.i(g,C);a.g(g,A);var j=[];a.f(D,function(l,e){var i=new z(l,e),d=i.qb,c=b.floor(e/f),k=e%f;a.m(d,(t+o)*k*(1-h));a.l(d,(s+p)*k*h);if(!j[c]){j[c]=a.N();a.v(g,j[c])}a.v(j[c],d);u.push(i)});var E=a.t({$AutoPlay:d,$NaviQuitDrag:d,$SlideWidth:n,$SlideHeight:l,$SlideSpacing:o*h+p*(1-h),$MinDragOffsetToSlide:12,$SlideDuration:200,$PauseOnHover:1,$PlayOrientation:e.$Orientation,$DragOrientation:e.$DisableDrag?0:e.$Orientation},e);r=new q(i,E);v=c}};h.lb=e=a.t({$SpacingX:3,$SpacingY:3,$DisplayPieces:1,$Orientation:1,$AutoCenter:3,$ActionMode:1},A);y=a.i(i);w=a.g(i);g=a.o(i,"slides",c);j=a.o(g,"prototype");t=a.i(j);s=a.g(j);a.W(g,j);f=e.$Lanes||1;o=e.$SpacingX;p=e.$SpacingY;k=e.$DisplayPieces};function r(){i.call(this,0,0);this.Mb=a.Ab}})(window,document,Math,null,true,false)