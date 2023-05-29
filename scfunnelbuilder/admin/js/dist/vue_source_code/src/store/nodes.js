import { defineStore } from "pinia";
import { checkForStepType, getOnlyNumber } from "@/utils/Common";

export const useNodeStore = defineStore("nodes", {
  state: () => ({
    funnelTitle: "New Automation",
    activeFunnelId: null,
    showModal: false,
    loading: false,
    activeModal: "0",
    stepsSidebarActive: true,
    isStatsVisible: false,
    latestEdge: "",
    activeStepData: {},
    dataFlow: [],
    stepsTemplateData: [],
    nodeClickedData: {},
    proceedParentNode: {},
    nodeClickedModal: false,
    conditionArray: [
      { label: "Yes", value: "YES", disabled: "false" },
      { label: "No", value: "NO", disabled: "false" },
    ],
    upDownSellArray: [
      { label: "Yes", value: "YES", disabled: "false" },
      { label: "No", value: "NO", disabled: "false" },
      { label: "Always", value: "ALWAYS", disabled: "false" },
    ],
    percentageArray: [
      { label: "Variant A", value: "A 40%", disabled: "false" },
      { label: "Variant B", value: "B 25%", disabled: "false" },
      { label: "Variant C", value: "C 35%", disabled: "false" },
    ],
    proceedArray: [],
    allPageList: [],
    allProductList: [],
  }),
  getters: {
    getStepsTemplateData(state) {
      return state.stepsTemplateData;
    },
    getAllProductList(state) {
      return state.allProductList;
    },
    getAllPageList(state) {
      return state.allPageList;
    },
    getFunnelId(state) {
      return state.activeFunnelId;
    },
    getFunnelTitle(state) {
      return state.funnelTitle;
    },
    getProceedParentNode(state) {
      return state.proceedParentNode;
    },
    getProceedArray(state) {
      return state.proceedArray;
    },
    getUpDownSellArray(state) {
      return state.upDownSellArray;
    },
    getConditionArray(state) {
      return state.conditionArray;
    },
    getPercentageArray(state) {
      return state.percentageArray;
    },

    getCustomPercentageArray(state) {
      const updatedArray = state.percentageArray.map((item) => {
        return {
          value: "" + getOnlyNumber(item.value),
          label: item.label,
          disabled: item.disabled,
        };
      });
      return updatedArray;
    },
    getNodeClickedData(state) {
      return state.nodeClickedData;
    },
    getNodeClickedModal(state) {
      return state.nodeClickedModal;
    },
    getActiveStep(state) {
      return state.activeStepData;
    },
    getEdgeId(state) {
      return state.latestEdge;
    },
    getDataFlow(state) {
      return state.dataFlow;
    },

    modalStatus(state) {
      return state.showModal;
    },
    statsVisibleStatus(state) {
      return state.isStatsVisible;
    },
    stepsSidebarStatus(state) {
      return state.stepsSidebarActive;
    },
    loadingStatus(state) {
      return state.loading;
    },
  },
  actions: {
    updateLoadingStatus(value) {
      this.loading = value;
    },
    updateUpDownsellArray(data) {
      this.upDownSellArray = data;
    },
    updateProceedParentNode(data) {
      this.proceedParentNode = data;
    },
    updatePercentageArray(data) {
      this.percentageArray = data;
    },
    updateConditionArray(data) {
      this.conditionArray = data;
    },
    updateProceedArray(data) {
      this.proceedArray = data;
    },
    updateNodeClickedData(nodeData) {
      this.nodeClickedData = nodeData;
    },
    updateActiveStepData(stepData) {
      this.activeStepData = stepData;
    },
    // add new edge
    addCustomEdge(data, nodeType) {
      const OnlyOneEdge = checkForStepType(nodeType, [
        "opt_in",
        "landing",
        "thankyou",
        "checkout",
      ]);
      const demo = this.dataFlow.find((x) => x.id === data.id);
      if (OnlyOneEdge) {
        // only one edge per node
        const checkOneEdge = this.dataFlow.filter(
          (x) => x.source === data.source
        );
        if (checkOneEdge.length) return;
      }
      if (!demo) {
        this.dataFlow.push(data);
      }
    },
    // add new Node
    addCustomNode(data) {
      this.dataFlow.push(data);
    },
    updateSelectedNode(newEdge) {
      let ed = this.dataFlow.find((v) => v.id === newEdge.id);
      if (ed && newEdge.labelStyle && newEdge.label) {
        ed.labelStyle = newEdge.labelStyle;
        ed.label = newEdge.label;
      }
      if (ed && newEdge.source && newEdge.target) {
        ed.source = newEdge.source;
        ed.target = newEdge.target;
      }
    },

    getNodeDetailsById(id) {
      return this.dataFlow.find((v) => v.id === id);
    },
    changeActiveModal(id) {
      this.activeModal = id;
    },

    deleteNode(nodeId) {
      const updatedArr = this.dataFlow.filter(
        (node) =>
          node.id !== nodeId && node.source !== nodeId && node.target !== nodeId
      );
      this.dataFlow = updatedArr;
    },
    deleteEdge(edgeId) {
      const updatedArr = this.dataFlow.filter((node) => node.id !== edgeId);
      this.dataFlow = updatedArr;
    },
    updateEdgeId(data) {
      this.latestEdge = data;
    },
    openModal() {
      this.showModal = true;
      document.body.classList.add("funnel_body");
    },
    closeModal() {
      this.showModal = false;
      document.body.classList.remove("funnel_body");
    },
    openNodeClickedModal() {
      this.nodeClickedModal = true;
    },
    closeNodeClickedModal() {
      this.nodeClickedModal = false;
    },
    closeStepsSidebar() {
      this.stepsSidebarActive = false;
    },
    toggleStepsSidebar() {
      this.stepsSidebarActive = !this.stepsSidebarActive;
    },
    toggleStatsVisibility() {
      this.isStatsVisible = !this.isStatsVisible;
    },
    updateFunnelTitle(value) {
      this.funnelTitle = value;
    },
    updateFunnelId(value) {
      this.activeFunnelId = value;
    },
    updatePageList(value) {
      this.allPageList = value;
    },
    updateProductList(value) {
      this.allProductList = value;
    },
    updateDataFlow(value) {
      this.dataFlow = value;
    },
    updateStepsTemplateData(value) {
      this.stepsTemplateData = value;
    },
  },
});
