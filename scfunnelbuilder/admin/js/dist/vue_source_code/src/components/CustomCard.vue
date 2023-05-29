<script setup>
import { ref } from "vue";
import placehoderImg from "@/assets/img/img_placeholder.svg";
import { Handle } from "@vue-flow/core";
import Proceed from "./nodes/Proceed.vue";
import NodeStats from "./nodes/NodeStats.vue";
import { useNodeStore } from "@/store/nodes";
import NodeAction from "./nodes/NodeAction.vue";
import { getConditionsData, getPercentageSpitData } from "@/api/funnel/funnel-service";
const props = defineProps({
  data: {
    type: Object,
  },
  activeHoverNodeId: String,
  nodeId: String,
  logicNodeId: String,
});
const emit = defineEmits(["onProceed"]);

const nodeStore = useNodeStore();
// conversion_rate,visit,conversion
const checkNodeType = (node_slug) => {
  const slugArray = ["percentage_split", "conditional_split"];

  return slugArray.includes(node_slug) ? true : false;
};
const checkProceedNodeType = (node_slug) => {
  const slugArray = [
    "percentage_split",
    "conditional_split",
    "upsell",
    "downsell",
  ];
  checkProceedType();
  return slugArray.includes(node_slug) ? true : false;
};

// check ProceedTypes
const checkProceedType = () => {
  if (nodeStore.getProceedParentNode.slug_type === "percentage_split") {
    const percentageArray = props.data?.percentage_split
      ? props.data?.percentage_split
      : nodeStore.getPercentageArray;
    nodeStore.updateProceedArray(percentageArray);
  } else if (nodeStore.getProceedParentNode.slug_type === "conditional_split") {
    const conditionalArray = props.data?.conditional_split
      ? props.data?.conditional_split
      : nodeStore.getConditionArray;
    nodeStore.updateProceedArray(conditionalArray);
  } else {
    const upDownsellArray = props.data?.upDownsell
      ? props.data?.upDownsell
      : nodeStore.getUpDownSellArray;
    nodeStore.updateProceedArray(upDownsellArray);
  }
};


const nodeClickHandler = async (data) => {
  const slugArray = ["percentage_split", "conditional_split"];
  const { step_id, slug_type } = data;
  if (slugArray.includes(slug_type)){
    if(step_id&&slug_type=="percentage_split"){
      nodeStore.updateActiveStepData(data);
      // await getPercentageSpitData(step_id);
      nodeStore.openModal();

    } else if(step_id&&slug_type=="conditional_split"){
      nodeStore.updateActiveStepData(data);
      nodeStore.openModal();
      // await getConditionsData(step_id)

    }
  }else{
    nodeStore.updateNodeClickedData(data); // for normal nodes open modal
    nodeStore.openNodeClickedModal();
    nodeStore.closeStepsSidebar();
  }
};
</script>

<template>
  <div
    class="node_card_container"
    @click="nodeClickHandler(data)"
    :key="nodeId"
    :class="[nodeId === activeHoverNodeId ? 'active_node_hover' : '']"
  >
    <div v-if="data?.targetVisible" class="cs_target">
      <Handle
        type="target"
        class="handler target_handler"
        position="top"
        connectable="true"
      />
    </div>
    <div class="body">
      <!-- for selected node .selected_node -->
      <div
        v-if="data?.slug_type === 'percentage_split'"
        class="percentage_split_node"
      >
        <div class="node_card_body ss">
          <div class="c_node_other">
            <h5>A/B</h5>
          </div>
          <h5 class="ms-2">Percentage Split</h5>
        </div>
      </div>
      <div
        v-else-if="data?.slug_type === 'conditional_split'"
        class="percentage_split_node conditional_split_node"
      >
        <div class="node_card_body">
          <div class="me-2">
            <img :src="data?.img_default" class="image" />
          </div>
          <h5>Check Conditions</h5>
        </div>
      </div>
      <div v-else class="node_card">
        <div class="node_card_body">
          <div class="img_wrap">
            <img
              :src="data?.img_default ? data?.img_default : placehoderImg"
              class="image"
            />
          </div>
          <div class="card_details card_name">
            <div class="card_info">
              <h5>{{ data?.title ? data?.title : "option" }}</h5>
              <p>
                {{ data?.step_title ? data?.step_title : data?.description }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-if="data?.sourceVisible" class="cs_source">
      <Handle
        class="handler source_handler"
        type="source"
        position="bottom"
        connectable="true"
      />
    </div>
  </div>
  <!-- when stats switch enabled -->
  <div
    v-if="!checkNodeType(data.slug_type) && nodeStore.statsVisibleStatus"
    class="stats_wrapper"
  >
    <NodeStats
      :conversion_rate_value="data?.conversion_rate"
      :conversion_value="data?.conversion"
      :visit_value="data?.visit"
    />
  </div>
  <div
    @click="nodeHoverHandler"
    class="action_dsfdsf"
    :class="[nodeId === activeHoverNodeId ? 'action_box_container' : '']"
  >
    <NodeAction :hoverNodeId="activeHoverNodeId" />
  </div>

  <div
    v-if="checkProceedNodeType(data.slug_type) && nodeId === logicNodeId"
    class="proceed_wrapper"
  >
    <!-- (data?.percentage_split) ? (data?.percentage_split) : -->
    <Proceed
      :proceedArray="nodeStore.getProceedArray"
      :selectedItem="
        ['upsell', 'downsell'].includes(
          nodeStore.getProceedParentNode.slug_type
        )
          ? 'Always'
          : ''
      "
      @on-submit="
        $emit('onProceed', {
          $event,
          edgeId: nodeStore.getEdgeId,
          slugType: data?.slug_type,
        })
      "
    />
  </div>
</template>
<style lang="scss">
.node_card_body {
  &.ss {
    padding: 19px !important;
  }
}
</style>
