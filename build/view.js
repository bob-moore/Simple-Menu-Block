import*as e from"@wordpress/interactivity";var t={d:(e,o)=>{for(var n in o)t.o(o,n)&&!t.o(e,n)&&Object.defineProperty(e,n,{enumerable:!0,get:o[n]})},o:(e,t)=>Object.prototype.hasOwnProperty.call(e,t)};const o=(r={getContext:()=>e.getContext,getElement:()=>e.getElement,store:()=>e.store},a={},t.d(a,r),a),n=JSON.parse('{"UU":"rcm/simple-menu"}'),{state:s,actions:c}=(0,o.store)(n.UU,{state:{focusedMenuItems:new Map},actions:{toggle:()=>{const e=(0,o.getContext)();e.isSubmenuActive=!e.isSubmenuActive},handleClick:()=>{c.toggle()},handleFocus:()=>{const e=(0,o.getContext)(),{ref:t}=(0,o.getElement)(),n=t.closest("[data-wp-context]");n&&!s.focusedMenuItems.has(n)&&s.focusedMenuItems.set(n,{ref:n,context:e}),e.isSubmenuActive=!0},handleHover:()=>{c.toggle()},handleBlur:()=>{(0,o.getContext)(),setTimeout((()=>{s.focusedMenuItems.forEach((e=>{e.ref.contains(document.activeElement)||e.ref===document.activeElement||(e.context.isSubmenuActive=!1,s.focusedMenuItems.delete(e.ref))}))}),0)}}});var r,a;