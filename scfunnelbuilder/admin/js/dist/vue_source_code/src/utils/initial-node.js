import checklist from "@/assets/icons/checklist.svg";
// import placehoderImg from "@/assets/img/img_placeholder.svg";
import thanku from "@/assets/img/thanku.svg";
import upsell from "@/assets/img/upsell.svg";
import landing from "@/assets/img/landing.svg";
import checkout from "@/assets/img/checkout.svg";

export const nodeList = [
  {
    title: "Steps",
    list: [
      {
        id: "1",
        nodeType: "custom",
        label: "Opt-in",
        data: {
          title: "Opt-in",
          sourceVisible: true,
          targetVisible: false,
          slug_type: "opt_in",
          description: "Step Description here",
          step_title: "",
        },
      },
      {
        id: "2",
        nodeType: "custom",
        label: "Landing",
        data: {
          sourceVisible: true,
          targetVisible: true,
          title: "Landing",
          slug_type: "landing",
          description: "Step Description here",
          step_title: "",
          img_default: landing,
        },
      },
      {
        id: "3",
        nodeType: "custom",
        label: "Checkout",
        data: {
          sourceVisible: true,
          targetVisible: true,
          title: "Checkout",
          slug_type: "checkout",
          description: "Step Description here",
          step_title: "",
          img_default: checkout,
        },
      },
      {
        id: "4",
        nodeType: "custom",
        label: "Upsell",
        data: {
          sourceVisible: true,
          targetVisible: true,
          title: "Upsell",
          slug_type: "upsell",
          description: "Step Description here",
          step_title: "",
          img_default: upsell,
        },
      },
      {
        id: "5",
        nodeType: "custom",
        label: "Downsell",
        data: {
          sourceVisible: true,
          targetVisible: true,
          title: "Downsell",
          slug_type: "downsell",
          description: "Step Description here",
          step_title: "",
          img_default: upsell,
        },
      },
      {
        id: "6",
        nodeType: "custom",
        label: "Thankyou",
        data: {
          sourceVisible: false,
          targetVisible: true,
          title: "Thank You",
          slug_type: "thankyou",
          description: "Step Description here",
          step_title: "",
          img_default: thanku,
        },
      },
    ],
  },
  {
    title: "Logic",
    list: [
      {
        id: "8",
        nodeType: "custom",
        label: "Conditional Split",
        data: {
          sourceVisible: true,
          targetVisible: true,
          title: "Conditional Split",
          slug_type: "conditional_split",
          description: "Step Description here",
          step_title: "",
          img_default: checklist,
        },
      },
      {
        id: "7",
        nodeType: "custom",
        label: "Percentage Split",
        data: {
          sourceVisible: true,
          targetVisible: true,
          title: "Percentage Split",
          slug_type: "percentage_split",
          description: "Step Description here",
          step_title: "",
          img_default: checklist,
        },
      },
    ],
  },
];
