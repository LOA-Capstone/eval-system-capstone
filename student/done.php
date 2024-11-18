<div class="container-fluid text-center">
    <!-- Loader -->
    <div class="loading" id="loader">
        <svg xmlns="http://www.w3.org/2000/svg" width="124" height="124" viewBox="0 0 124 124">
            <circle class="circle-loading" cx="62" cy="62" r="59" fill="none" stroke="hsl(140, 40%, 74%)" stroke-width="6px"></circle>
            <circle class="circle" cx="62" cy="62" r="59" fill="none" stroke="hsl(140, 40%, 53%)" stroke-width="6px" stroke-linecap="round"></circle>
            <polyline class="check" points="73.56 48.63 57.88 72.69 49.38 62" fill="none" stroke="hsl(140, 40%, 53%)" stroke-width="6px" stroke-linecap="round"></polyline>
        </svg>
    </div>

    <!-- Thank-you Message and Countdown -->
    <a href="./index.php" id="thank-you-message" style="color: hsl(140, 40%, 53%); font-weight: bold; display: block;">
        You are done evaluating! Thank you!
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

    .check {
        stroke-dasharray: 45;
        stroke-dashoffset: 45;
        animation: check_4 0.2s 2s ease-in-out forwards;
    }

    @keyframes circle_4 {
        0% {
            stroke-dashoffset: 380;
        }
        100% {
            stroke-dashoffset: 0;
        }
    }

    @keyframes check_4 {
        0% {
            stroke-dashoffset: 45;
        }
        100% {
            stroke-dashoffset: 90;
        }
    }

</style>

