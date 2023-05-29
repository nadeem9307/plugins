import { useNodeStore } from "@/store/nodes";
import BaseApi from "@/api/BaseApi";
import { toast } from "vue3-toastify";
import { checkForStepType } from "@/utils/Common";
export const toastOption = {
  transition: "slide",
  position: "bottom-right",
  autoClose: 3000,
  Position: "bottom-right",
};
const getFunnelDetails = async (id) => {
  const nodeStore = useNodeStore();
  nodeStore.updateFunnelId(id);
  let loading;
  let error;
  let response;
  try {
    const apiData = await BaseApi.get(`getFunnel?funnel_id=${id}`);
    const { data } = apiData;
    if (data) {
      response = data;
      const { funnel_data, title } = data;
      title ? nodeStore.updateFunnelTitle(title) : null;
      funnel_data ? nodeStore.updateDataFlow(funnel_data) : null;
    }
  } catch ({ response }) {
    error = response?.data?.message;
    if (!error) return;
    toast.error(response?.data?.message, toastOption);
  } finally {
    allPages();
    allProducts();
    loading = false;
    return {
      loading,
      error,
      response,
    };
  }
};

// save funnel data
const saveFunnel = async (isFunnelEnabled = false) => {
  const nodeStore = useNodeStore();
  const funnelData = {
    funnel_id: nodeStore.getFunnelId,
    funnel_data: nodeStore.getDataFlow,
    funnel_enabled: isFunnelEnabled,
    funnel_identifier: [],
    mintSteps: [],
    should_update_steps: true,
    should_update_steps_order: true,
  };
  try {
    const resData = await BaseApi.create("saveFunnel", funnelData);
    const { data } = resData;
    if (data) {
      const { funnel_data } = data;
      nodeStore.updateDataFlow(funnel_data);
    }
  } catch ({ response }) {
    let error = response?.data?.message;
    if (!error) return;
    toast.error(response?.data?.message, toastOption);
  }
};

// clone Funnel

const cloneFunnel = async () => {
  const nodeStore = useNodeStore();
  const allFunnelData = nodeStore.getDataFlow;
  try {
    const resData = await BaseApi.create("clone_funnel", allFunnelData);
    const { data } = resData;
    if (data) {
    }
  } catch ({ response }) {
    let error = response?.data?.message;
    if (!error) return;
    toast.error(response?.data?.message, toastOption);
  }
};

// delete Funnel
const deleteFunnel = async () => {
  const nodeStore = useNodeStore();

  try {
    const resData = await BaseApi.get("delete_funnel", nodeStore.getFunnelId);
    const { data } = resData;
    if (data) {
    }
  } catch ({ response }) {
    let error = response?.data?.message;
    if (!error) return;
    toast.error(response?.data?.message, toastOption);
  }
};

// add new node
const addNewNode = async (stepData) => {
  const nodeStore = useNodeStore();
  let response;
  let error;
  try {
    const resData = await BaseApi.create("create_step", stepData);
    const { data } = resData;
    if (data) {
      let restData;
      const { success, funnel_data, ...rest } = data;
      if (checkForStepType(stepData.slug_type, ["upsell", "downsell"])) {
        restData = {
          ...rest,
          upDownsell: [...nodeStore.getUpDownSellArray],
        };
      } else {
        restData = { ...rest };
      }
      const updatedArray = nodeStore.getDataFlow?.map((item) => {
        if (
          !item?.data?.step_node_id &&
          item?.data?.slug_type === stepData?.slug_type
        ) {
          return {
            ...item,
            data: {
              ...restData,
              ...item.data,
            },
          };
        }
        return item;
      });
      response = updatedArray;
      nodeStore.updateDataFlow([...updatedArray]);
    }
  } catch ({ response }) {
    error = response?.data?.message;
    toast.error(response?.data?.message, toastOption);
  } finally {
    return {
      error,
      response,
    };
  }
};
// update node details
const updateStepNode = async (stepData, nodeId) => {
  const nodeStore = useNodeStore();
  let error;
  try {
    const resData = await BaseApi.create("update_steps", stepData);
    const { data } = resData;
    if (data) {
      const { success, ...rest } = data;
      const updatedArray = nodeStore.getDataFlow.map((item) => {
        if (item.id == nodeId) {
          return {
            ...item,
            data: {
              ...rest,
              ...item.data,
            },
          };
        }
        return item;
      });
      nodeStore.updateDataFlow(updatedArray);
    }
  } catch ({ response }) {
    error = response?.data?.message;
    if (!error) return;
    toast.error(response?.data?.message, toastOption);
  } finally {
    return {
      error,
    };
  }
};

// update funnel tittle
const updateFunnelTitle = async (funnel_title) => {
  try {
    const resData = await BaseApi.create("update_funnel", funnel_title);
    const { data } = resData;
    if (data) {
    }
  } catch ({ response }) {
    let error = response?.data?.message;
    if (!error) return;
    toast.error(response?.data?.message, toastOption);
  }
};

// import a template to the Node
const importTemplate = async (templateData) => {
  const nodeStore = useNodeStore();
  let error;
  let response;
  try {
    const resData = await BaseApi.create("import_step", templateData);
    const { data } = resData;
    if (data) {
      response = data;
      const { success, ...rest } = data;
      const updatedArray = nodeStore.getDataFlow.map((item) => {
        if (
          !item?.data?.step_node_id &&
          item?.data?.slug_type === templateData?.slug_type
        ) {
          return {
            ...item,
            data: {
              ...item.data,
              ...rest,
            },
          };
        }
        return item;
      });
      nodeStore.updateDataFlow(updatedArray);
    }
  } catch ({ response }) {
    error = response?.data?.message;
    if (!error) return;
    toast.error(response?.data?.message, toastOption);
  } finally {
    return {
      error,
      response,
    };
  }
};
// get All templates
const getStepTemplates = async (templateParams) => {
  const nodeStore = useNodeStore();
  try {
    const resData = await BaseApi.get(
      `get_templates?slug_type=${templateParams}`
    );
    const { data } = resData;
    if (data) {
      const { templates } = data;
      const modifiedTemplate = templates?.map((item) => {
        return {
          page_title: item?.title?.rendered,
          download_link: item?.acf?.download_link,
          preview_link: item?.acf?.preview_link,
          category: item?.filters?.collection
            ?.map((item) => item.name)
            .join(" "),
          featured_image: item?.featured_image,
        };
      });
      nodeStore.updateStepsTemplateData(modifiedTemplate);
    }
  } catch ({ response }) {
    toast.error(response?.data?.message, toastOption);
  }
};

// get a node
const deleteStepNode = async (stepId, nodeId, edgeLabel) => {
  const nodeStore = useNodeStore();
  let edgeParam;
  let error;
  let resolve;
  try {
    if (edgeLabel) {
      edgeLabel = edgeLabel?.replace(/%/g, "");
      edgeParam = `&variation_value=${edgeLabel}`;
    } else {
      edgeParam = "";
    }
    const resData = await BaseApi.get(
      `delete_step?funnel_id=${nodeStore.getFunnelId}&step_id=${stepId}${edgeParam}`
    );
    const { data } = resData;
    if (data) {
      nodeStore.deleteNode(nodeId);
      resolve = data;
      // await saveFunnel();
    }
  } catch ({ response }) {
    error = response?.data?.message;
    if (!error) return;
    toast.error(response?.data?.message, toastOption);
  } finally {
    return {
      resolve,
      error,
    };
  }
};

const deletePercentageEdge = async (edge_label, slug_type, sourceId) => {
  const nodeStore = useNodeStore();
  let error;
  let resolve;
  try {
    const resData = await BaseApi.get(
      `delete_percentage_connection?funnel_id=${nodeStore.getFunnelId}&variation_value=${edge_label}&step_node_id=${sourceId}-${slug_type}`
    );
    const { data } = resData;
    if (data) {
      resolve = data;
      // await saveFunnel();
    }
  } catch ({ response }) {
    error = response?.data?.message;
    if (!error) return;
    toast.error(response?.data?.message, toastOption);
  } finally {
    return {
      resolve,
      error,
    };
  }
};
// update condition
const updateConditionCode = async (splitData) => {
  let error;
  let response;
  try {
    const resData = await BaseApi.create("update_conditional_node", splitData);
    const { data } = resData;
    if (data) {
      response = data;
    }
  } catch ({ response }) {
    error = response?.data?.message;
    if (!error) return;
    toast.error(response?.data?.message, toastOption);
  } finally {
    return {
      error,
      response,
    };
  }
};

// save all conditions
const saveConditions = async (conditionParams, slug_type) => {
  let error;
  const nodeStore = useNodeStore();
  try {
    const resData = await BaseApi.create(
      "saveConditionalNode",
      conditionParams
    );
    const { data } = resData;
    if (data) {
      let restData;
      if (slug_type == "conditional_split") {
        restData = {
          conditional_split: [...nodeStore.getConditionArray],
        };
      } else {
        restData = undefined;
      }
      const updatedArray = nodeStore.getDataFlow?.map((item) => {
        if (item?.data?.slug_type === slug_type) {
          return {
            ...item,
            data: {
              ...item.data,
              ...restData,
            },
          };
        }
        return item;
      });
      nodeStore.updateDataFlow(updatedArray);
    }
  } catch ({ response }) {
    error = response?.data?.message;
    toast.error(response?.data?.message, toastOption);
  } finally {
    return {
      error,
    };
  }
};

// save all PercentageSplits
const savePercentageSplits = async (percentageParams, slug_type) => {
  const nodeStore = useNodeStore();
  let error;
  try {
    const resData = await BaseApi.create(
      "create_percentage_split_settings",
      percentageParams
    );

    const { data } = resData;
    if (data) {
      let restData;
      if (slug_type == "percentage_split") {
        restData = {
          percentage_split: [...nodeStore.getPercentageArray],
        };
      } else {
        restData = undefined;
      }
      const updatedArray = nodeStore.getDataFlow?.map((item) => {
        if (item?.data?.slug_type === slug_type) {
          return {
            ...item,
            data: {
              ...item.data,
              ...restData,
            },
          };
        }
        return item;
      });
      nodeStore.updateDataFlow(updatedArray);
    }
  } catch ({ response }) {
    error = response?.data?.message;
    toast.error(response?.data?.message, toastOption);
  } finally {
    return {
      error,
    };
  }
};
// get saved PercentageData
const getPercentageSpitData = async (nodeId) => {
  const nodeStore = useNodeStore();
  let error;
  let responseData;
  try {
    const resData = await BaseApi.get(
      `get_percentage_split_data?funnel_id=${nodeStore.getFunnelId}&step_node_id=${nodeId}`
    );
    const { data } = resData;
    if (data) {
      const { data: percentData } = data;
      if (percentData?.variations) {
        responseData = percentData;
        // nodeStore.updatePercentageArray(percentData?.variations);
      }
    }
  } catch ({ response }) {
    error = response?.data?.message;
    toast.error(response?.data?.message, toastOption);
  } finally {
    return {
      error,
      responseData
    };
  }
};
// get saved conditional Data
const getConditionsData = async (nodeId) => {
  const nodeStore = useNodeStore();
  let error;
  let responseData;
  try {
    const resData = await BaseApi.get(
      `get_conditional_node?funnel_id=${nodeStore.getFunnelId}&step_node_id=${nodeId}`
    );
    const { data } = resData;
    if (data) {
      const { data: conditionData } = data;
      if (conditionData?.conditions) {
        responseData = data?.data;
        // nodeStore.updatePercentageArray(conditionData?.conditions);
      }
    }
  } catch ({ response }) {
    error = response?.data?.message;
    toast.error(response?.data?.message, toastOption);
  } finally {
    return {
      error,
      responseData,
    };
  }
};
// split/update all Percentage

const splitsPercentageToNodes = async (splitData) => {
  let error;
  let response;
  try {
    const resData = await BaseApi.create(
      "update_percentage_split_node",
      splitData
    );
    const { data } = resData;
    if (data.status) {
      response = data;
    }
  } catch ({ response }) {
    error = response?.data?.message;
    toast.error(response?.data?.message, toastOption);
  } finally {
    return {
      error,
      response,
    };
  }
};

// list of all products
const allProducts = async () => {
  const nodeStore = useNodeStore();
  let response;
  let error;
  try {
    const resData = await BaseApi.get("getProducts");
    const { data } = resData;
    if (data) {
      response = response;
      nodeStore.updateProductList(data);
    }
  } catch ({ response }) {
    error = response?.data?.message;
    if (!error) return;
    toast.error(response?.data?.message, toastOption);
  } finally {
    return {
      error,
      response,
    };
  }
};
// list of all price Plans
const allPricePlans = async (productId) => {
  let response;
  let error;
  try {
    const resData = await BaseApi.get(
      `get_product_info?productId=${productId}`
    );
    const { data } = resData;
    if (data) {
      const modifiedArray = data.map((item) => {
        return {
          value: item.option_name ? item.option_name : item.option_id,
          price: item.price,
          label: item.option_name ? item.option_name : item.option_id,
        };
      });
      response = modifiedArray;
    }
  } catch ({ response }) {
    error = response?.data?.message;
    toast.error(response?.data?.message, toastOption);
  } finally {
    return {
      error,
      response,
    };
  }
};

// get All Pages
const allPages = async () => {
  const nodeStore = useNodeStore();
  try {
    const resData = await BaseApi.get("get_all_pages");
    const { data } = resData;
    if (data) {
      nodeStore.updatePageList(data);
    }
  } catch ({ response }) {
    toast.error(response?.data?.message, toastOption);
  }
};

// condition final result dropdown list based on condition 1 and  Condition 2
const getConfirmationList = async (data) => {
  const { product_type, productId } = data;
  let error;
  let response;
  try {
    const resData = await BaseApi.get(
      `get_conditions_confirmation?product_type=${product_type}&productId=${productId}`
    );
    const { data } = resData;
    if (data) {
      response = data;
    }
  } catch ({ response }) {
    error = response?.data?.message;
    if (!error) return;
    toast.error(response?.data?.message, toastOption);
  } finally {
    return {
      error,
      response,
    };
  }
};

export {
  getFunnelDetails,
  saveFunnel,
  allPages,
  allProducts,
  allPricePlans,
  addNewNode,
  saveConditions,
  getConditionsData,
  updateConditionCode,
  deletePercentageEdge,
  deleteStepNode,
  splitsPercentageToNodes,
  getConfirmationList,
  savePercentageSplits,
  getPercentageSpitData,
  getStepTemplates,
  cloneFunnel,
  deleteFunnel,
  importTemplate,
  updateStepNode,
  updateFunnelTitle,
};
