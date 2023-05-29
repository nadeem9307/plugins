<script setup>
import { VueFlow, MarkerType, useVueFlow } from "@vue-flow/core";
import { nextTick, reactive, watch } from "vue";
import AllStep from "./AllStep.vue";
import angle_left from "@/assets/icons/left_angle_white.svg";
import angle_right from "@/assets/icons/right_angle_white.svg";
import CustomCard from "../CustomCard.vue";
import { useNodeStore } from "@/store/nodes";
import { ref } from "vue";
import BaseButton from "../buttons/BaseButton.vue";
import Icon from "../Icons/Icon.vue";
import zoom_in from "@/assets/icons/zoom_in.svg";
import zoom_out from "@/assets/icons/zoom_out.svg";
import {
  checkForStepType,
  updateEdgesStatus,
  getAllEdges,
  randomId,
} from "@/utils/Common";
import {
  getFunnelDetails,
  splitsPercentageToNodes,
  updateConditionCode,
} from "@/api/funnel/funnel-service";
import { deletePercentageEdge } from "@/api/funnel/funnel-service";
import { saveFunnel } from "@/api/funnel/funnel-service";
let vueFlowEle = ref(null);
let activeHoverNode = ref(null);
let logicNode = ref(null);
const nodeStore = useNodeStore();

let nodeCustomData = reactive({
  data: {},
});

const {
  findNode,
  addNodes,
  removeEdges,
  updateEdge,
  zoomIn,
  zoomOut,
  project,
  vueFlowRef,
} = useVueFlow({});

// dagged over from step sidebar to view panel

function onDragOver(event) {
  event.preventDefault();

  if (event.dataTransfer) {
    event.dataTransfer.dropEffect = "move";
  }
}

// when a edge is connected to the
const onConnect = (e) => {
  const { source, target } = e;

  if (source === target) return;
  logicNode.value = source;
  // to get source node details
  const { data } = nodeStore.getNodeDetailsById(source);
  nodeStore.updateProceedParentNode(data);
  nodeStore.updateEdgeId(`${source}-${target}`);
  const newEdge = {
    id: `${source}-${target}`,
    source: `${source}`,
    target: `${target}`,
    type: "default",
    markerEnd: {
      type: MarkerType.ArrowClosed,
      width: 20,
      height: 20,
      color: "#000000",
    },
    labelBgPadding: [8, 4],
    labelBgBorderRadius: 4,
    labelBgStyle: { fill: "#f5f5f5", color: "#000" },
    style: {
      strokeWidth: 2,
      stroke: "#000000",
    },
  };
  if (
    ["upsell", "conditional_split", "percentage_split", "downsell"].includes(
      data.slug_type
    )
  ) {
    newEdge.updatable = true;
  }

  nodeStore.addCustomEdge(newEdge, data?.slug_type);
};

// drop a node to main view
function onDrop(event) {
  const type = event.dataTransfer?.getData("application/vueflow");
  if (type !== "custom") return;
  const { left, top } = vueFlowRef.value.getBoundingClientRect();
  const position = project({
    x: event.clientX - left,
    y: event.clientY - top,
  });
  const newNode = {
    id: `${randomId()}`,
    type,
    position,
    data: nodeCustomData.data,
    label: `${type}`,
  };
  nodeStore.addCustomNode(newNode);

  // align node position after drop, so it's centered to the mouse
  nextTick(() => {
    const node = findNode(newNode.id);
    const stop = watch(
      () => node.dimensions,
      (dimensions) => {
        if (dimensions.width > 0 && dimensions.height > 0) {
          node.position = {
            x: node.position.x - node.dimensions.width / 2,
            y: node.position.y - node.dimensions.height / 2,
          };
          stop();
        }
      },
      { deep: true, flush: "post" }
    );
  });
  // open modal
  nodeStore.closeStepsSidebar();
  nodeStore.openModal();
  nodeStore.updateActiveStepData(nodeCustomData.data);
}

// get data od draged node from left side steps  section
const nodeDraggedHandler = (data) => {
  const nodeData = {
    sourceVisible: data?.sourceVisible,
    targetVisible: data?.targetVisible,
    title: data?.title,
    slug_type: data?.slug_type,
    description: data?.description,
    img_default: data?.img_default,
  };
  nodeCustomData.data = nodeData;
};

// add hover on node
const nodeHoverHandler = ({ connectedEdges, node, event }) => {
  activeHoverNode.value = node.id;
};
// remove hover from node
const nodeMouseLeaveHandler = () => {
  activeHoverNode.value = "";
};

// on proceed submit add label to edge
const onProceedHandler = async (e) => {
  const stepType = "always";
  const { edgeId, slugType } = e;

  const newEdge = {
    id: e.edgeId,
    label: stepType.toLowerCase() === e.$event?.toLowerCase() ? "" : e.$event,
    labelStyle: {
      fontWeight: "600",
      fontSize: "13px",
      fill:
        e.$event === "YES" ? "#05C237" : e.$event === "NO" ? "#F71616" : "#000",
    },
  };

  const [source, target] = edgeId.split("-");
  // var newSource = source.substring(1);
  const percentageSelected = {
    funnel_id: nodeStore.getFunnelId,
    source_node_id: source,
    target_node_id: target,
    variation_value: e.$event.toUpperCase(),
  };

  if (stepType.toLowerCase() === e.$event?.toLowerCase()) {
    logicNode.value = "";
    const { error, response } = await updateConditionCode(percentageSelected);
    if (error || response) nodeStore.updateLoadingStatus(false);
    logicNode.value = "";
    if (error) {
      removeEdges([edgeId]);
      nodeStore.deleteEdge(edgeId);
    }
    await saveFunnel();
    return;
  }
  // send data to api
  nodeStore.updateLoadingStatus(true);
  if (checkForStepType(slugType, ["upsell", "downsell", "conditional_split"])) {
    const { error, response } = await updateConditionCode(percentageSelected);
    if (error || response) nodeStore.updateLoadingStatus(false);
    logicNode.value = "";
    if (error) {
      removeEdges([edgeId]);
      nodeStore.deleteEdge(edgeId);
      // return;
    } else if (response) {
      // await saveFunnel();
    }

    if (checkForStepType(slugType, ["upsell", "downsell"])) {
      const tempArray = updateEdgesStatus("upDownsell", e.$event, "true");
      nodeStore.updateDataFlow(tempArray);
    } else {
      const tempArray = updateEdgesStatus(
        "conditional_split",
        e.$event,
        "true"
      );
      nodeStore.updateDataFlow(tempArray);
    }
  } else {
    const { error, response } = await splitsPercentageToNodes(
      percentageSelected
    );
    if (error || response) nodeStore.updateLoadingStatus(false);
    logicNode.value = "";
    if (response) {
      const tempArray = updateEdgesStatus("percentage_split", e.$event, "true");
      nodeStore.updateDataFlow(tempArray);
      // await saveFunnel();
    } else {
      removeEdges([edgeId]);
      nodeStore.deleteEdge(edgeId);
      // await saveFunnel();
    }
  }
  nodeStore.updateLoadingStatus(false);
  logicNode.value = "";
  nodeStore.updateSelectedNode(newEdge);
  await saveFunnel();
};

// delete edge on double click
const edgeClickHandler = async ({ edge, event }) => {
  const { sourceNode } = edge;
  if (
    checkForStepType(sourceNode?.data?.slug_type, [
      "upsell",
      "downsell",
      "conditional_split",
      "percentage_split",
    ])
  ) {
    if (sourceNode?.data?.slug_type == "percentage_split") {
      const edge_label = edge?.label?.replace(/%/g, "");
      if (edge_label) {
        const { error, resolve } = await deletePercentageEdge(
          edge_label,
          sourceNode?.data?.slug_type,
          sourceNode?.id
        );
      }
      const tempArray = updateEdgesStatus(
        "percentage_split",
        edge?.label,
        "false"
      );
      nodeStore.updateDataFlow(tempArray);
    } else if (sourceNode?.data?.slug_type == "conditional_split") {
      if (edge?.label) {
        const { error, resolve } = await deletePercentageEdge(
          edge?.label,
          sourceNode?.data?.slug_type,
          sourceNode?.id
        );
      }
      const tempArray = updateEdgesStatus(
        "conditional_split",
        edge?.label,
        "false"
      );
      nodeStore.updateDataFlow(tempArray);
    } else {
      const edgeLabel = edge?.label ? edge?.label : "ALWAYS";
      if (edgeLabel) {
        const { error, resolve } = await deletePercentageEdge(
          edgeLabel,
          sourceNode?.data?.slug_type,
          sourceNode?.id
        );
      }
      const tempArray = updateEdgesStatus("upDownsell", edgeLabel, "false");
      nodeStore.updateDataFlow(tempArray);
    }
  }
  removeEdges([edge.id]);
  nodeStore.deleteEdge(edge.id);
  await saveFunnel();
};

// zoomout handler
const zoomOutHandler = () => {
  zoomOut();
};

// zoom in Handler
const zoomInHandler = () => {
  zoomIn();
};

// update Edge of Nodes
const edgeUpdateHandler = ({ edge, connection }) => {
  const { source, target } = connection;
  edge.id = `${source}-${target}`;
  const newEdge = {
    id: edge.id,
    source: source,
    target: target,
  };
  updateEdge(edge, connection);
  nodeStore.updateSelectedNode(newEdge);
};
</script>

<template>
  <!-- fit-view-on-init pan-on-scroll-mode="vertical" :prevent-scrolling="false" :zoom-on-pinch="false" :zoom-on-scroll="false" -->
  <div class="dndflow steps_sidebar_dnd" @drop="onDrop">
    <VueFlow
      ref="vueFlowEle"
      v-model="nodeStore.dataFlow"
      @edge-update="edgeUpdateHandler"
      @edge-double-click="edgeClickHandler"
      @dragover="onDragOver"
      @connect="onConnect"
      @node-mouse-enter="nodeHoverHandler"
      @node-mouse-leave="nodeMouseLeaveHandler"
      :default-edge-options="{ type: 'straight', edgesFocusable: true }"
    >
      <template #node-custom="{ id, data }">
        <CustomCard
          :key="id"
          @on-proceed="onProceedHandler"
          :nodeId="id"
          :activeHoverNodeId="activeHoverNode"
          :logicNodeId="logicNode"
          :data="data"
        />
      </template>
    </VueFlow>

    <AllStep @get-draged-node-data="nodeDraggedHandler" />

    <div class="control_panel">
      <div class="ctrl_btns">
        <BaseButton class="zoom_in _ctrl" @on-click="zoomInHandler">
          <Icon :name="zoom_in" />
        </BaseButton>
        <BaseButton class="zoom_out _ctrl" @on-click="zoomOutHandler">
          <Icon :name="zoom_out" />
        </BaseButton>
      </div>
    </div>
  </div>
</template>
<style></style>
