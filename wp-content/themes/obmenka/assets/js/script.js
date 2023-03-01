!function(){"use strict";window.addEventListener("DOMContentLoaded",(()=>{(()=>{let e=(e,t)=>{if(t.focus(),t.setSelectionRange)t.setSelectionRange(e,e);else if(t.createTextRange){let a=t.createTextRange();a.collapse(!0),a.moveEnd("character",e),a.moveStart("character",e),a.select()}};function t(t){t.target.getAttribute("data-mask")&&("tel"==t.target.getAttribute("type")||"text"==t.target.getAttribute("type")&&t.target.classList.contains("card-validate"))&&function(t){const a=t.target;let s=a.getAttribute("data-mask"),n=0,r=s.replace(/\D/g,""),i=a.value.replace(/\D/g,"");r.length>=i.length&&(i=r),a.value=s.replace(/./g,(function(e){return/[_\d]/.test(e)&&n<i.length?i.charAt(n++):n>=i.length?"":e})),"blur"===t.type?2==a.value.length&&(a.value=""):e(a.value.length,a)}(t)}window.addEventListener("input",t),window.addEventListener("focus",t),window.addEventListener("blur",t)})(),function(){let e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"";if(e){const t=document.querySelector(e);window.addEventListener("scroll",(()=>{document.documentElement.scrollTop>1650?(t.classList.add("animated","fadeIn"),t.classList.remove("fadeOut")):(t.classList.add("fadeOut"),t.classList.remove("fadeIn"))}))}let t=document.querySelectorAll('[href^="#"]'),a=.3;t.forEach((e=>{e.addEventListener("click",(function(e){e.preventDefault();let t=document.documentElement.scrollTop,s=this.hash;if(document.querySelector(s)){let n=document.querySelector(s).getBoundingClientRect().top,r=null;function i(e){null===r&&(r=e);let c=e-r,l=n<0?Math.max(t-c/a,t+n):Math.min(t+c/a,t+n);document.documentElement.scrollTo(0,l),l!=t+n?requestAnimationFrame(i):location.hash=s}requestAnimationFrame(i)}else e.target.getAttribute("data-url")&&(window.location.href=e.target.getAttribute("data-url"))}))}))}(),(()=>{try{const e=function(e){let t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:[],a=arguments.length>2&&void 0!==arguments[2]?arguments[2]:0;e.forEach((e=>e.classList.remove("active"))),e[a].classList.add("active"),t&&(t.forEach((e=>e.classList.remove("active"))),t[a].classList.add("active"))};document.querySelectorAll(".slider-default").forEach((t=>{const a=t.querySelectorAll(".slider-item"),s=t.querySelector(".slider-points");let n=[];s&&a.forEach((e=>{const t=document.createElement("span");s.append(t),n.push(t)})),e(a,n);let r=a.length-1,i=0;setInterval((()=>{i==r?i=0:i++,e(a,n,i)}),4e3),n.forEach(((t,s)=>{t.addEventListener("click",(()=>{e(a,n,s),i=s}))}))}))}catch(e){console.log(e.stack)}})(),(()=>{try{function e(){document.querySelector("body").classList.remove("fixed"),document.querySelector("html").classList.remove("fixed")}function t(){document.querySelector(".modal").classList.remove("active"),document.querySelectorAll(".modal__item").forEach((e=>e.classList.remove()))}function a(e){const t=document.querySelector(e);t.classList.add("active"),t.parentElement.classList.add("active"),document.querySelector("body").classList.add("fixed"),document.querySelector("html").classList.add("fixed")}function s(e){let t=0;for(let a=0;a<e.length;a++)t+=+e.substring(a,a+1);let a=[0,1,2,3,4,-4,-3,-2,-1,0];for(let s=e.length-1;s>=0;s-=2)t+=a[+e.substring(s,s+1)];let s=t%10;return s=10-s,10==s&&(s=0),s}function n(e){let t=+(e=e.replace(/\s/g,"")).substring(e.length-1,e.length);return s(e.substring(0,e.length-1))==+t}function r(e){let t=arguments.length>1&&void 0!==arguments[1]&&arguments[1];if(!e)return;let a=document.cookie.match(new RegExp("(?:^|; )"+e.replace(/([.$?*|{}()\[\]\\\/+^])/g,"\\$1")+"=([^;]*)"));if(a){let e=decodeURIComponent(a[1]);if(t)try{return JSON.parse(e)}catch(e){}return e}}function i(e,t){let a=arguments.length>2&&void 0!==arguments[2]?arguments[2]:{path:"/"};if(!e)return;a=a||{},a.expires instanceof Date&&(a.expires=a.expires.toUTCString()),t instanceof Object&&(t=JSON.stringify(t));let s=encodeURIComponent(e)+"="+encodeURIComponent(t);for(let e in a){s+="; "+e;let t=a[e];!0!==t&&(s+="="+t)}document.cookie=s}function c(e){i(e,null,{expires:new Date,path:"/"})}function l(e,t,a){return e||(e=0),((t=+t.replace(",",".")*e)/(a=+a.replace(",","."))).toFixed(2)}async function o(e){let t=await fetch(e,{method:"GET"});return await t.text()}const u=document.querySelector(".change-form-cont");let d=[];function g(){let e=!(arguments.length>0&&void 0!==arguments[0])||arguments[0];t(),e&&(u.innerHTML+='<div class="main-loading"><img src="/wp-content/themes/obmenka/assets/images/loading.gif" alt=""></div>'),o(u.getAttribute("data-url")+"?action=form_steps").then((e=>{u.innerHTML=e}))}function m(){setInterval((()=>{o(u.getAttribute("data-url")+"?action=check_status&post_id="+r("order-post-id")).then((e=>{e=JSON.parse(e),Array.isArray(e)?(i("step",3,{path:"/",expires:2592e3}),i("status","fail",{path:"/",expires:2592e3}),g()):e&&(i("step",3,{path:"/",expires:2592e3}),i("status","succes",{path:"/",expires:2592e3}),g())}))}),1e4)}function v(){let e="?action=create_order";d.forEach((t=>{e+="&"+t.name+"="+t.value})),u.innerHTML+='<div class="main-loading"><img src="/wp-content/themes/obmenka/assets/images/loading.gif" alt=""></div>',o(u.getAttribute("data-url")+e).then((e=>{i("order-post-id",e,{path:"/",expires:2592e3}),i("step",2,{path:"/",expires:2592e3}),g(!1)}))}g(),u.innerHTML+='<div class="main-loading"><img src="/wp-content/themes/obmenka/assets/images/loading.gif" alt=""></div>';const p=document.querySelector(".modal__check label");p.addEventListener("click",(e=>{p.querySelector(".checkbox").classList.remove("invalid"),p.classList.toggle("active")})),2==r("step")&&"get-money"==r("status")&&m(),3==r("step")&&setTimeout((()=>{dclearAllCookies()}),5e3),window.addEventListener("keyup",(e=>{if("send-sum"==e.target.getAttribute("name")){let t=document.querySelector('input[name="get-sum"]');t.value=l(e.target.value,e.target.getAttribute("data-rubs"),t.getAttribute("data-rubs"))}if("get-sum"==e.target.getAttribute("name")){let t=document.querySelector('input[name="send-sum"]');t.value=l(e.target.value,e.target.getAttribute("data-rubs"),t.getAttribute("data-rubs"))}e.target.classList.contains("card-validate")&&(n(e.target.value)?(e.target.closest(".cards-item").querySelector(".cards-invalid").textContent="",e.target.classList.remove("invalid")):(e.target.closest(".cards-item").querySelector(".cards-invalid").textContent="Проверьте номер карты",e.target.classList.add("invalid")))})),document.body.addEventListener("click",(s=>{if((s.target.classList.contains("invalid")||s.target.closest(".invalid"))&&(s.target.classList.contains("invalid")?s.target:s.target.closest(".invalid")).classList.remove("invalid"),s.target.classList.contains("back")&&(e(),t()),s.target.classList.contains("list_items-val")&&(s.target.closest(".list_target").classList.contains("target-currs")&&setTimeout((()=>{i("send-curr",document.querySelector('input[name="send-curr"]').value,{path:"/",expires:2592e3}),i("get-curr",document.querySelector('input[name="get-curr"]').value,{path:"/",expires:2592e3}),c("get-bank"),c("send-bank"),g()}),500),s.target.closest(".list_target").classList.contains("target-banks")&&setTimeout((()=>{i("send-bank",document.querySelector('input[name="send-bank"]').value,{path:"/",expires:2592e3}),i("get-bank",document.querySelector('input[name="get-bank"]').value,{path:"/",expires:2592e3}),g()}),500)),s.target.classList.contains("delete-order")&&(u.innerHTML+='<div class="main-loading"><img src="/wp-content/themes/obmenka/assets/images/loading.gif" alt=""></div>',o(u.getAttribute("data-url")+"?action=delete_order&post_id="+r("order-post-id")).then((()=>{c("order-post-id"),c("step"),c("status"),c("get-curr"),c("send-curr"),c("get-bank"),c("send-bank"),g(!1)}))),s.target.classList.contains("continue")&&(document.querySelector("#privacy").checked?(e(),t(),v()):p.querySelector(".checkbox").classList.add("invalid")),s.target.classList.contains("continue-pay")&&(e(),t(),i("status","get-money",{path:"/",expires:2592e3}),m(),g()),s.target.classList.contains("pay-done")&&a("#pay-done"),s.target.classList.contains("main__form-change-button")){const e=u.querySelector('input[name="send-bank"]'),t=u.querySelector('input[name="get-bank"]'),s=u.querySelector('input[name="send-sum"]'),n=u.querySelector('input[name="get-sum"]'),r=u.querySelector('input[name="contacts"]'),i=u.querySelector('input[name="send-card"]'),c=u.querySelector('input[name="get-card"]'),l=u.querySelector('input[name="send-curr"]'),o=u.querySelector('input[name="get-curr"]');let g=!0,m=[e,t,r];[s,n,i,c].forEach((e=>{e.value&&!e.classList.contains("invalid")||(e.classList.add("invalid"),g=!1)})),m.forEach((e=>{e.value||(e.closest(".field").classList.add("invalid"),g=!1)})),g&&(a("#how-work"),d=[],d.push({name:"send-bank",value:e.value}),d.push({name:"get-bank",value:t.value}),d.push({name:"send-sum",value:s.value}),d.push({name:"get-sum",value:n.value}),d.push({name:"contacts",value:r.value}),d.push({name:"send-card",value:i.value}),d.push({name:"get-card",value:c.value}),d.push({name:"send-curr",value:l.value}),d.push({name:"get-curr",value:o.value}))}}))}catch(h){console.log(h.stack)}})(),(()=>{try{const e=document.querySelector(".header__hamburger"),t=document.querySelector(".header__nav");document.body.addEventListener("click",(a=>{a.target==e&&(e.classList.toggle("active"),t.classList.toggle("active")),a.target==e||a.target.closest("header__nav")||a.target==t||(e.classList.remove("active"),t.classList.remove("active"))}))}catch(e){console.log(e.stack)}try{document.body.addEventListener("click",(e=>{if(e.target.closest(".list_target")||e.target.classList.contains("list_target")?(e.target.classList.contains("list_target")?e.target:e.target.closest(".list_target")).classList.toggle("active"):document.querySelectorAll(".list_target").forEach((e=>e.classList.remove("active"))),e.target.classList.contains("list_items-val")){const t=e.target.closest(".list_target");t.classList.contains("input-change")?(t.querySelector(".list_input").value="",t.querySelector(".list_input").placeholder=e.target.getAttribute("data-value").trim(),t.querySelector(".list_input").type=e.target.getAttribute("data-type").trim()):(t.querySelector(".list_input").value=e.target.getAttribute("data-value").trim(),t.querySelector(".list_text").innerHTML=e.target.getAttribute("data-value").trim()),e.target.getAttribute("data-img")&&(t.querySelector(".list_img").src=e.target.getAttribute("data-img")),t.querySelectorAll(".list_items-val").forEach((e=>e.style.display="")),e.target.style.display="none"}}))}catch(e){console.log(e.stack)}})()}))}();