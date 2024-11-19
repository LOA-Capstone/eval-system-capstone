<div class="container-fluid text-center">
    <!-- Loader -->
    <div class="loading" id="loader">
        <svg xmlns="http://www.w3.org/2000/svg" width="124" height="124" viewBox="0 0 124 124">
            <circle class="circle-loading" cx="62" cy="62" r="59" fill="none" stroke="hsl(0, 60%, 74%)" stroke-width="6px"></circle>
            <circle class="circle" cx="62" cy="62" r="59" fill="none" stroke="hsl(0, 60%, 53%)" stroke-width="6px" stroke-linecap="round"></circle>
            <line class="cross-line" x1="45" y1="45" x2="79" y2="79" stroke="hsl(0, 60%, 53%)" stroke-width="6px" stroke-linecap="round"></line>
            <line class="cross-line" x1="79" y1="45" x2="45" y2="79" stroke="hsl(0, 60%, 53%)" stroke-width="6px" stroke-linecap="round"></line>
        </svg>
    </div>

    <!-- Closed Message -->
    <a href="./index.php" id="closed-message" style="color: hsl(0, 60%, 53%); font-weight: bold; display: block;">
        The evaluation is closed. Please come back later!
    </a>
</div>

<style>
    #uni_modal .modal-footer {
        display: none;
    }

    #uni_modal .modal-footer.display {
        display: flex;
    }

    .loading {
        width: 124px;
        height: 124px;
        margin: 50px auto; /* Center loader */
        display: block;
        position: relative;
        animation: rotate 2s linear infinite;
    }

    .loading svg {
        display: block;
        width: 100%;
        height: 100%;
    }

    .circle {
        transform: rotate(-90deg);
        transform-origin: center;
        stroke-dasharray: 380;
        stroke-dashoffset: 380;
        animation: circle_4 2s ease-in-out forwards;
    }

    .cross-line {
        stroke-dasharray: 50;
        stroke-dashoffset: 50;
        animation: cross_4 0.2s 2s ease-in-out forwards;
    }

    @keyframes circle_4 {
        0% {
            stroke-dashoffset: 380;
        }
        100% {
            stroke-dashoffset: 0;
        }
    }

    @keyframes cross_4 {
        0% {
            stroke-dashoffset: 50;
        }
        100% {
            stroke-dashoffset: 0;
        }
    }
</style>
