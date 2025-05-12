export interface Step {
    badge: string;
    dance: string;
    name: string;
    steps: FootStep[] | null;
}


export interface FootStep {
    howquick: number;
    m1_x: number;
    m1_y: number;
    m1_rotate: number;
    m1_toe: boolean;
    m1_heel: boolean;
    m2_x: number;
    m2_y: number;
    m2_rotate: number;
    m2_toe: boolean;
    m2_heel: boolean;
    w1_x: number;
    w1_y: number;
    w1_rotate: number;
    w1_toe: boolean;
    w1_heel: boolean;
    w2_x: number;
    w2_y: number;
    w2_rotate: number;
    w2_toe: boolean;
    w2_heel: boolean;
}

export interface FootAnimationProps {
    currentStep: Step | null;
    loaderIsVisible: boolean;
    danceStepCounter: number;
    danceStepLength: number;
    autoplayVariable: string;
    danceName: string;
    isInEditMode: boolean;
}

export interface FootAnimationEmits {
    'backToBeginBtn': void;
    'backBtn': void;
    'autoplayBtn': void;
    'nextBtn': void;
    'backToEndBtn': void;
    'addStep': void;
    'removeStep': void;
    'backToMenu': void;
}