<div class="container-fluid text-center">
    <!-- Loader -->
    <div class="loading" id="loader">
        <svg xmlns="http://www.w3.org/2000/svg" width="124" height="124" viewBox="0 0 124 124">
            <circle class="circle-loading" cx="62" cy="62" r="59" fill="none" stroke="hsl(50, 60%, 74%)" stroke-width="6px"></circle>
            <circle class="circle" cx="62" cy="62" r="59" fill="none" stroke="hsl(50, 60%, 53%)" stroke-width="6px" stroke-linecap="round"></circle>
            <text class="question-mark" x="62" y="78" fill="hsl(50, 60%, 53%)" font-size="48px" font-family="Arial, sans-serif" text-anchor="middle">?</text>
        </svg>
    </div>

    <!-- Not Started Message -->
    <a href="./index.php" id="not-started-message" style="color: hsl(50, 60%, 53%); font-weight: bold; display: block;">
        The evaluation has not started yet. Please check back later!
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

    .question-mark {
        opacity: 0;
        animation: fade_in 2s ease-in-out 1.5s forwards;
    }

    @keyframes circle_4 {
        0% {
            stroke-dashoffset: 380;
        }
        100% {
            stroke-dashoffset: 0;
        }
    }

    @keyframes fade_in {
        0% {
            opacity: 0;
        }
        100% {
            opacity: 1;
        }
    }
</style>
