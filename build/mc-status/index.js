(()=>{"use strict";var e,r={808:(e,r,t)=>{const n=window.wp.blocks,o=window.React,l=window.wp.i18n,a=window.wp.serverSideRender;var s=t.n(a);const c=window.wp.blockEditor,i=window.wp.components,u=JSON.parse('{"UU":"mc-server-info/mc-status"}');(0,n.registerBlockType)(u.UU,{edit:function({attributes:e,setAttributes:r}){const t=(0,o.createElement)(c.BlockControls,{group:"block"}),n=(0,o.createElement)(c.InspectorControls,null,(0,o.createElement)(i.Panel,null,(0,o.createElement)(i.PanelBody,null,(0,o.createElement)(i.PanelRow,null,(0,o.createElement)(i.TextControl,{label:(0,l.__)("Address","mc-server-info"),help:(0,l.__)('The Minecraft server address, excluding the protocol (e.g., "http" or "https").',"mc-server-info"),value:e.address,onChange:e=>{const t=e.replace(/^(http:\/\/|https:\/\/)/,"");r({address:t})}})),(0,o.createElement)(i.PanelRow,null,(0,o.createElement)(i.TextControl,{label:(0,l.__)("Port","mc-server-info"),help:(0,l.__)("Port of the Minecraft Server","mc-server-info"),value:e.port,onChange:e=>r({port:e})})),(0,o.createElement)(i.PanelRow,null,(0,o.createElement)(i.TextControl,{label:(0,l.__)("Dynmap URL","mc-server-info"),help:(0,l.__)("The url of a Dynmap with http(s)","mc-server-info"),value:e.dynurl,onChange:e=>r({dynurl:e})})))));return(0,o.createElement)("p",{...(0,c.useBlockProps)()},t,n,(0,o.createElement)(s(),{block:"mc-server-info/mc-status",attributes:e}))},save:function(){return null}})}},t={};function n(e){var o=t[e];if(void 0!==o)return o.exports;var l=t[e]={exports:{}};return r[e](l,l.exports,n),l.exports}n.m=r,e=[],n.O=(r,t,o,l)=>{if(!t){var a=1/0;for(u=0;u<e.length;u++){for(var[t,o,l]=e[u],s=!0,c=0;c<t.length;c++)(!1&l||a>=l)&&Object.keys(n.O).every((e=>n.O[e](t[c])))?t.splice(c--,1):(s=!1,l<a&&(a=l));if(s){e.splice(u--,1);var i=o();void 0!==i&&(r=i)}}return r}l=l||0;for(var u=e.length;u>0&&e[u-1][2]>l;u--)e[u]=e[u-1];e[u]=[t,o,l]},n.n=e=>{var r=e&&e.__esModule?()=>e.default:()=>e;return n.d(r,{a:r}),r},n.d=(e,r)=>{for(var t in r)n.o(r,t)&&!n.o(e,t)&&Object.defineProperty(e,t,{enumerable:!0,get:r[t]})},n.o=(e,r)=>Object.prototype.hasOwnProperty.call(e,r),(()=>{var e={491:0,43:0};n.O.j=r=>0===e[r];var r=(r,t)=>{var o,l,[a,s,c]=t,i=0;if(a.some((r=>0!==e[r]))){for(o in s)n.o(s,o)&&(n.m[o]=s[o]);if(c)var u=c(n)}for(r&&r(t);i<a.length;i++)l=a[i],n.o(e,l)&&e[l]&&e[l][0](),e[l]=0;return n.O(u)},t=globalThis.webpackChunkmc_server_info=globalThis.webpackChunkmc_server_info||[];t.forEach(r.bind(null,0)),t.push=r.bind(null,t.push.bind(t))})();var o=n.O(void 0,[43],(()=>n(808)));o=n.O(o)})();