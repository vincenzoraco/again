<?php

namespace VincenzoRaco\Again;

enum AgainStopReason
{
    case CONDITION_MET;
    case MAX_ITERATIONS_REACHED;
}
