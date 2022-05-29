import { ready, newInstance } from "@jsplumb/browser-ui"
import { AnchorPlacement, AnchorLocations, DotEndpoint } from "@jsplumb/core"

ready(() => {

    // list of possible anchor locations for the blue source element
    const sourceAnchors:Array<AnchorPlacement> = [
        [ 0, 1, 0, 1 ],
        [ 0.25, 1, 0, 1 ],
        [ 0.5, 1, 0, 1 ],
        [ 0.75, 1, 0, 1 ],
        [ 1, 1, 0, 1 ]
    ];

    const canvas = document.getElementById("canvas")

    const instance = newInstance({
        // drag options
        dragOptions: { cursor: "pointer", zIndex: 2000 },
        paintStyle: {
            stroke: "#558822",
            strokeWidth: 10
        },
        container: canvas
    })

    // click listener for the enable/disable link in the source box (the blue one).
    instance.on(document.getElementById("enableDisableSource"), "click", (e:Event) => {
        const sourceDiv = ((e.target|| e.srcElement) as Element).parentNode as HTMLElement
        const state = instance.toggleSourceEnabled(sourceDiv)
        document.getElementById("enableDisableSource").innerHTML = (state ? "disable" : "enable")
        instance[state ? "removeClass" : "addClass"](sourceDiv, "element-disabled")
        instance.consume(e)
    });

    // click listener for enable/disable in the small green boxes
    instance.on(document.getElementById("canvas"), "click", ".enableDisableTarget",  (e:Event) => {
        const targetDiv = ((e.target|| e.srcElement) as Element).parentNode as HTMLElement
        const state = instance.toggleTargetEnabled(targetDiv)
        document.getElementById("canvas").innerHTML = (state ? "disable" : "enable")
        instance[state ? "removeClass" : "addClass"](targetDiv, "element-disabled")
        instance.consume(e)
    });

    // get the list of ".smallWindow" elements.            
    const smallWindows = document.querySelectorAll(".smallWindow");

    smallWindows.forEach(function(el) { instance.manage(el); });

    // suspend drawing and initialise.
    instance.batch(function () {

        // make 'window1' a connection source. notice the filter and filterExclude parameters: they tell jsPlumb to ignore drags
        // that started on the 'enable/disable' link on the blue window.
        instance.makeSource(document.getElementById("sourceWindow1"), {
            filter:"a",
            filterExclude:true,
            maxConnections: -1,
            endpoint:{type:DotEndpoint.type, options:{ radius: 7, cssClass:"small-blue" } },
            anchor:sourceAnchors
        });

        // configure the .smallWindows as targets.
        smallWindows.forEach(function(el) {
            instance.makeTarget(el, {
                anchor:AnchorLocations.Top,
                endpoint:{type:DotEndpoint.type, options:{ radius: 11, cssClass:"large-green" } }
            });
        });

        // and finally connect a couple of small windows, just so its obvious what's going on when this demo loads.           
        instance.connect({ source: document.getElementById("sourceWindow1"), target: document.getElementById("targetWindow5") });
        instance.connect({ source: document.getElementById("sourceWindow1"), target: document.getElementById("targetWindow2") });
    });
});	
