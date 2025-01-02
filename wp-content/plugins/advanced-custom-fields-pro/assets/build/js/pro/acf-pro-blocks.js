/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./src/advanced-custom-fields-pro/assets/src/js/pro/_acf-blocks.js":
/*!*************************************************************************!*\
  !*** ./src/advanced-custom-fields-pro/assets/src/js/pro/_acf-blocks.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "./node_modules/react/index.js");


function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { (0,_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
const md5 = __webpack_require__(/*! md5 */ "./node_modules/md5/md5.js");
(($, undefined) => {
  // Dependencies.
  const {
    BlockControls,
    InspectorControls,
    InnerBlocks,
    useBlockProps,
    AlignmentToolbar,
    BlockVerticalAlignmentToolbar
  } = wp.blockEditor;
  const {
    ToolbarGroup,
    ToolbarButton,
    Placeholder,
    Spinner
  } = wp.components;
  const {
    Fragment
  } = wp.element;
  const {
    Component
  } = React;
  const {
    withSelect
  } = wp.data;
  const {
    createHigherOrderComponent
  } = wp.compose;

  // Potentially experimental dependencies.
  const BlockAlignmentMatrixToolbar = wp.blockEditor.__experimentalBlockAlignmentMatrixToolbar || wp.blockEditor.BlockAlignmentMatrixToolbar;
  // Gutenberg v10.x begins transition from Toolbar components to Control components.
  const BlockAlignmentMatrixControl = wp.blockEditor.__experimentalBlockAlignmentMatrixControl || wp.blockEditor.BlockAlignmentMatrixControl;
  const BlockFullHeightAlignmentControl = wp.blockEditor.__experimentalBlockFullHeightAligmentControl || wp.blockEditor.__experimentalBlockFullHeightAlignmentControl || wp.blockEditor.BlockFullHeightAlignmentControl;
  const useInnerBlocksProps = wp.blockEditor.__experimentalUseInnerBlocksProps || wp.blockEditor.useInnerBlocksProps;

  /**
   * Storage for registered block types.
   *
   * @since 5.8.0
   * @var object
   */
  const blockTypes = {};

  /**
   * Returns a block type for the given name.
   *
   * @date	20/2/19
   * @since	5.8.0
   *
   * @param	string name The block name.
   * @return	(object|false)
   */
  function getBlockType(name) {
    return blockTypes[name] || false;
  }

  /**
   * Returns a block version for a given block name
   *
   * @date 8/6/22
   * @since 6.0
   *
   * @param string name The block name
   * @return int
   */
  function getBlockVersion(name) {
    const blockType = getBlockType(name);
    return blockType.acf_block_version || 1;
  }

  /**
   * Returns true if a block (identified by client ID) is nested in a query loop block.
   *
   * @date 17/1/22
   * @since 5.12
   *
   * @param {string} clientId A block client ID
   * @return boolean
   */
  function isBlockInQueryLoop(clientId) {
    const parents = wp.data.select('core/block-editor').getBlockParents(clientId);
    const parentsData = wp.data.select('core/block-editor').getBlocksByClientId(parents);
    return parentsData.filter(block => block.name === 'core/query').length;
  }

  /**
   * Returns true if we're currently inside the WP 5.9+ site editor.
   *
   * @date 08/02/22
   * @since 5.12
   *
   * @return boolean
   */
  function isSiteEditor() {
    return typeof pagenow === 'string' && pagenow === 'site-editor';
  }

  /**
   * Returns true if the block editor is currently showing the desktop device type preview.
   *
   * This function will always return true in the site editor as it uses the
   * edit-post store rather than the edit-site store.
   *
   * @date 15/02/22
   * @since 5.12
   *
   * @return boolean
   */
  function isDesktopPreviewDeviceType() {
    const editPostStore = select('core/edit-post');

    // Return true if the edit post store isn't available (such as in the widget editor)
    if (!editPostStore) return true;

    // Check if function exists (experimental or not) and return true if it's Desktop, or doesn't exist.
    if (editPostStore.__experimentalGetPreviewDeviceType) {
      return 'Desktop' === editPostStore.__experimentalGetPreviewDeviceType();
    } else if (editPostStore.getPreviewDeviceType) {
      return 'Desktop' === editPostStore.getPreviewDeviceType();
    } else {
      return true;
    }
  }

  /**
   * Returns true if the block editor is currently in template edit mode.
   *
   * @date 16/02/22
   * @since 5.12
   *
   * @return boolean
   */
  function isEditingTemplate() {
    const editPostStore = select('core/edit-post');

    // Return false if the edit post store isn't available (such as in the widget editor)
    if (!editPostStore) return false;

    // Return false if the function doesn't exist
    if (!editPostStore.isEditingTemplate) return false;
    return editPostStore.isEditingTemplate();
  }

  /**
   * Returns true if we're currently inside an iFramed non-desktop device preview type (WP5.9+)
   *
   * @date 15/02/22
   * @since 5.12
   *
   * @return boolean
   */
  function isiFramedMobileDevicePreview() {
    return $('iframe[name=editor-canvas]').length && !isDesktopPreviewDeviceType();
  }

  /**
   * Registers a block type.
   *
   * @date	19/2/19
   * @since	5.8.0
   *
   * @param	object blockType The block type settings localized from PHP.
   * @return	object The result from wp.blocks.registerBlockType().
   */
  function registerBlockType(blockType) {
    // Bail early if is excluded post_type.
    const allowedTypes = blockType.post_types || [];
    if (allowedTypes.length) {
      // Always allow block to appear on "Edit reusable Block" screen.
      allowedTypes.push('wp_block');

      // Check post type.
      const postType = acf.get('postType');
      if (!allowedTypes.includes(postType)) {
        return false;
      }
    }

    // Handle svg HTML.
    if (typeof blockType.icon === 'string' && blockType.icon.substr(0, 4) === '<svg') {
      const iconHTML = blockType.icon;
      blockType.icon = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(Div, null, iconHTML);
    }

    // Remove icon if empty to allow for default "block".
    // Avoids JS error preventing block from being registered.
    if (!blockType.icon) {
      delete blockType.icon;
    }

    // Check category exists and fallback to "common".
    const category = wp.blocks.getCategories().filter(_ref => {
      let {
        slug
      } = _ref;
      return slug === blockType.category;
    }).pop();
    if (!category) {
      //console.warn( `The block "${blockType.name}" is registered with an unknown category "${blockType.category}".` );
      blockType.category = 'common';
    }

    // Merge in block settings before local additions.
    blockType = acf.parseArgs(blockType, {
      title: '',
      name: '',
      category: '',
      api_version: 2,
      acf_block_version: 1
    });

    // Remove all empty attribute defaults from PHP values to allow serialisation.
    // https://github.com/WordPress/gutenberg/issues/7342
    for (const key in blockType.attributes) {
      if (blockType.attributes[key].default.length === 0) {
        delete blockType.attributes[key].default;
      }
    }

    // Apply anchor supports to avoid block editor default writing to ID.
    if (blockType.supports.anchor) {
      blockType.attributes.anchor = {
        type: 'string'
      };
    }

    // Append edit and save functions.
    let ThisBlockEdit = BlockEdit;
    let ThisBlockSave = BlockSave;

    // Apply alignText functionality.
    if (blockType.supports.alignText || blockType.supports.align_text) {
      blockType.attributes = addBackCompatAttribute(blockType.attributes, 'align_text', 'string');
      ThisBlockEdit = withAlignTextComponent(ThisBlockEdit, blockType);
    }

    // Apply alignContent functionality.
    if (blockType.supports.alignContent || blockType.supports.align_content) {
      blockType.attributes = addBackCompatAttribute(blockType.attributes, 'align_content', 'string');
      ThisBlockEdit = withAlignContentComponent(ThisBlockEdit, blockType);
    }

    // Apply fullHeight functionality.
    if (blockType.supports.fullHeight || blockType.supports.full_height) {
      blockType.attributes = addBackCompatAttribute(blockType.attributes, 'full_height', 'boolean');
      ThisBlockEdit = withFullHeightComponent(ThisBlockEdit, blockType.blockType);
    }

    // Set edit and save functions.
    blockType.edit = props => (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(ThisBlockEdit, props);
    blockType.save = () => (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(ThisBlockSave, null);

    // Add to storage.
    blockTypes[blockType.name] = blockType;

    // Register with WP.
    const result = wp.blocks.registerBlockType(blockType.name, blockType);

    // Fix bug in 'core/anchor/attribute' filter overwriting attribute.
    // Required for < WP5.9
    // See https://github.com/WordPress/gutenberg/issues/15240
    if (result.attributes.anchor) {
      result.attributes.anchor = {
        type: 'string'
      };
    }

    // Return result.
    return result;
  }

  /**
   * Returns the wp.data.select() response with backwards compatibility.
   *
   * @date	17/06/2020
   * @since	5.9.0
   *
   * @param	string selector The selector name.
   * @return	mixed
   */
  function select(selector) {
    if (selector === 'core/block-editor') {
      return wp.data.select('core/block-editor') || wp.data.select('core/editor');
    }
    return wp.data.select(selector);
  }

  /**
   * Returns the wp.data.dispatch() response with backwards compatibility.
   *
   * @date	17/06/2020
   * @since	5.9.0
   *
   * @param	string selector The selector name.
   * @return	mixed
   */
  function dispatch(selector) {
    return wp.data.dispatch(selector);
  }

  /**
   * Returns an array of all blocks for the given args.
   *
   * @date	27/2/19
   * @since	5.7.13
   *
   * @param	{object} args An object of key=>value pairs used to filter results.
   * @return	array.
   */
  function getBlocks(args) {
    let blocks = [];

    // Local function to recurse through all child blocks and add to the blocks array.
    const recurseBlocks = block => {
      blocks.push(block);
      select('core/block-editor').getBlocks(block.clientId).forEach(recurseBlocks);
    };

    // Trigger initial recursion for parent level blocks.
    select('core/block-editor').getBlocks().forEach(recurseBlocks);

    // Loop over args and filter.
    for (const k in args) {
      blocks = blocks.filter(_ref2 => {
        let {
          attributes
        } = _ref2;
        return attributes[k] === args[k];
      });
    }

    // Return results.
    return blocks;
  }

  /**
   * Storage for the AJAX queue.
   *
   * @const {array}
   */
  const ajaxQueue = {};

  /**
   * Storage for cached AJAX requests for block content.
   *
   * @since 5.12
   * @const {array}
   */
  const fetchCache = {};

  /**
   * Fetches a JSON result from the AJAX API.
   *
   * @date	28/2/19
   * @since	5.7.13
   *
   * @param	object block The block props.
   * @query	object The query args used in AJAX callback.
   * @return	object The AJAX promise.
   */
  function fetchBlock(args) {
    const {
      attributes = {},
      context = {},
      query = {},
      clientId = null,
      delay = 0
    } = args;

    // Build a unique queue ID from block data, including the clientId for edit forms.
    const queueId = md5(JSON.stringify(_objectSpread(_objectSpread(_objectSpread({}, attributes), context), query)));
    const data = ajaxQueue[queueId] || {
      query: {},
      timeout: false,
      promise: $.Deferred(),
      started: false
    };

    // Append query args to storage.
    data.query = _objectSpread(_objectSpread({}, data.query), query);
    if (data.started) return data.promise;

    // Set fresh timeout.
    clearTimeout(data.timeout);
    data.timeout = setTimeout(() => {
      data.started = true;
      if (fetchCache[queueId]) {
        ajaxQueue[queueId] = null;
        data.promise.resolve.apply(fetchCache[queueId][0], fetchCache[queueId][1]);
      } else {
        $.ajax({
          url: acf.get('ajaxurl'),
          dataType: 'json',
          type: 'post',
          cache: false,
          data: acf.prepareForAjax({
            action: 'acf/ajax/fetch-block',
            block: JSON.stringify(attributes),
            clientId: clientId,
            context: JSON.stringify(context),
            query: data.query
          })
        }).always(() => {
          // Clean up queue after AJAX request is complete.
          ajaxQueue[queueId] = null;
        }).done(function () {
          fetchCache[queueId] = [this, arguments];
          data.promise.resolve.apply(this, arguments);
        }).fail(function () {
          data.promise.reject.apply(this, arguments);
        });
      }
    }, delay);

    // Update storage.
    ajaxQueue[queueId] = data;

    // Return promise.
    return data.promise;
  }

  /**
   * Returns true if both object are the same.
   *
   * @date	19/05/2020
   * @since	5.9.0
   *
   * @param	object obj1
   * @param	object obj2
   * @return	bool
   */
  function compareObjects(obj1, obj2) {
    return JSON.stringify(obj1) === JSON.stringify(obj2);
  }

  /**
   * Converts HTML into a React element.
   *
   * @date	19/05/2020
   * @since	5.9.0
   *
   * @param	string html The HTML to convert.
   * @param	int acfBlockVersion The ACF block version number.
   * @return	object Result of React.createElement().
   */
  acf.parseJSX = (html, acfBlockVersion) => {
    // Apply a temporary wrapper for the jQuery parse to prevent text nodes triggering errors.
    html = '<div>' + html + '</div>';
    // Correctly balance InnerBlocks tags for jQuery's initial parse.
    html = html.replace(/<InnerBlocks([^>]+)?\/>/, '<InnerBlocks$1></InnerBlocks>');
    return parseNode($(html)[0], acfBlockVersion, 0).props.children;
  };

  /**
   * Converts a DOM node into a React element.
   *
   * @date	19/05/2020
   * @since	5.9.0
   *
   * @param	DOM node The DOM node.
   * @param	int acfBlockVersion The ACF block version number.
   * @param	int level The recursion level.
   * @return	object Result of React.createElement().
   */
  function parseNode(node, acfBlockVersion) {
    let level = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 0;
    // Get node name.
    const nodeName = parseNodeName(node.nodeName.toLowerCase(), acfBlockVersion);
    if (!nodeName) {
      return null;
    }

    // Get node attributes in React friendly format.
    const nodeAttrs = {};
    if (level === 1 && nodeName !== 'ACFInnerBlocks') {
      // Top level (after stripping away the container div), create a ref for passing through to ACF's JS API.
      nodeAttrs.ref = React.createRef();
    }
    acf.arrayArgs(node.attributes).map(parseNodeAttr).forEach(_ref3 => {
      let {
        name,
        value
      } = _ref3;
      nodeAttrs[name] = value;
    });
    if ('ACFInnerBlocks' === nodeName) {
      return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(ACFInnerBlocks, nodeAttrs);
    }

    // Define args for React.createElement().
    const args = [nodeName, nodeAttrs];
    acf.arrayArgs(node.childNodes).forEach(child => {
      if (child instanceof Text) {
        const text = child.textContent;
        if (text) {
          args.push(text);
        }
      } else {
        args.push(parseNode(child, acfBlockVersion, level + 1));
      }
    });

    // Return element.
    return React.createElement.apply(this, args);
  }

  /**
   * Converts a node or attribute name into it's JSX compliant name
   *
   * @date     05/07/2021
   * @since    5.9.8
   *
   * @param    string name The node or attribute name.
   * @return  string
   */
  function getJSXName(name) {
    const replacement = acf.isget(acf, 'jsxNameReplacements', name);
    if (replacement) return replacement;
    return name;
  }

  /**
   * Converts the given name into a React friendly name or component.
   *
   * @date	19/05/2020
   * @since	5.9.0
   *
   * @param	string name The node name in lowercase.
   * @param	int acfBlockVersion The ACF block version number.
   * @return	mixed
   */
  function parseNodeName(name, acfBlockVersion) {
    switch (name) {
      case 'innerblocks':
        if (acfBlockVersion < 2) {
          return InnerBlocks;
        }
        return 'ACFInnerBlocks';
      case 'script':
        return Script;
      case '#comment':
        return null;
      default:
        // Replace names for JSX counterparts.
        name = getJSXName(name);
    }
    return name;
  }

  /**
   * Functional component for ACFInnerBlocks.
   *
   * @since 6.0.0
   *
   * @param obj props element properties.
   * @return DOM element
   */
  function ACFInnerBlocks(props) {
    const {
      className = 'acf-innerblocks-container'
    } = props;
    const innerBlockProps = useInnerBlocksProps({
      className: className
    }, props);
    return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)("div", innerBlockProps, innerBlockProps.children);
  }

  /**
   * Converts the given attribute into a React friendly name and value object.
   *
   * @date	19/05/2020
   * @since	5.9.0
   *
   * @param	obj nodeAttr The node attribute.
   * @return	obj
   */
  function parseNodeAttr(nodeAttr) {
    let name = nodeAttr.name;
    let value = nodeAttr.value;
    switch (name) {
      // Class.
      case 'class':
        name = 'className';
        break;

      // Style.
      case 'style':
        const css = {};
        value.split(';').forEach(s => {
          const pos = s.indexOf(':');
          if (pos > 0) {
            let ruleName = s.substr(0, pos).trim();
            const ruleValue = s.substr(pos + 1).trim();

            // Rename core properties, but not CSS variables.
            if (ruleName.charAt(0) !== '-') {
              ruleName = acf.strCamelCase(ruleName);
            }
            css[ruleName] = ruleValue;
          }
        });
        value = css;
        break;

      // Default.
      default:
        // No formatting needed for "data-x" attributes.
        if (name.indexOf('data-') === 0) {
          break;
        }

        // Replace names for JSX counterparts.
        name = getJSXName(name);

        // Convert JSON values.
        const c1 = value.charAt(0);
        if (c1 === '[' || c1 === '{') {
          value = JSON.parse(value);
        }

        // Convert bool values.
        if (value === 'true' || value === 'false') {
          value = value === 'true';
        }
        break;
    }
    return {
      name,
      value
    };
  }

  /**
   * Higher Order Component used to set default block attribute values.
   *
   * By modifying block attributes directly, instead of defining defaults in registerBlockType(),
   * WordPress will include them always within the saved block serialized JSON.
   *
   * @date	31/07/2020
   * @since	5.9.0
   *
   * @param	Component BlockListBlock The BlockListBlock Component.
   * @return	Component
   */
  const withDefaultAttributes = createHigherOrderComponent(BlockListBlock => class WrappedBlockEdit extends Component {
    constructor(props) {
      super(props);

      // Extract vars.
      const {
        name,
        attributes
      } = this.props;

      // Only run on ACF Blocks.
      const blockType = getBlockType(name);
      if (!blockType) {
        return;
      }

      // Check and remove any empty string attributes to match PHP behaviour.
      Object.keys(attributes).forEach(key => {
        if (attributes[key] === '') {
          delete attributes[key];
        }
      });

      // Backward compatibility attribute replacement.
      const upgrades = {
        full_height: 'fullHeight',
        align_content: 'alignContent',
        align_text: 'alignText'
      };
      Object.keys(upgrades).forEach(key => {
        if (attributes[key] !== undefined) {
          attributes[upgrades[key]] = attributes[key];
        } else if (attributes[upgrades[key]] === undefined) {
          //Check for a default
          if (blockType[key] !== undefined) {
            attributes[upgrades[key]] = blockType[key];
          }
        }
        delete blockType[key];
        delete attributes[key];
      });

      // Set default attributes for those undefined.
      for (let attribute in blockType.attributes) {
        if (attributes[attribute] === undefined && blockType[attribute] !== undefined) {
          attributes[attribute] = blockType[attribute];
        }
      }
    }
    render() {
      return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(BlockListBlock, this.props);
    }
  }, 'withDefaultAttributes');
  wp.hooks.addFilter('editor.BlockListBlock', 'acf/with-default-attributes', withDefaultAttributes);

  /**
   * The BlockSave functional component.
   *
   * @date	08/07/2020
   * @since	5.9.0
   */
  function BlockSave() {
    return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(InnerBlocks.Content, null);
  }

  /**
   * The BlockEdit component.
   *
   * @date	19/2/19
   * @since	5.7.12
   */
  class BlockEdit extends Component {
    constructor(props) {
      super(props);
      this.setup();
    }
    setup() {
      const {
        name,
        attributes,
        clientId
      } = this.props;
      const blockType = getBlockType(name);

      // Restrict current mode.
      function restrictMode(modes) {
        if (!modes.includes(attributes.mode)) {
          attributes.mode = modes[0];
        }
      }
      if (isBlockInQueryLoop(clientId) || isSiteEditor() || isiFramedMobileDevicePreview() || isEditingTemplate()) {
        restrictMode(['preview']);
      } else {
        switch (blockType.mode) {
          case 'edit':
            restrictMode(['edit', 'preview']);
            break;
          case 'preview':
            restrictMode(['preview', 'edit']);
            break;
          default:
            restrictMode(['auto']);
            break;
        }
      }
    }
    render() {
      const {
        name,
        attributes,
        setAttributes,
        clientId
      } = this.props;
      const blockType = getBlockType(name);
      const forcePreview = isBlockInQueryLoop(clientId) || isSiteEditor() || isiFramedMobileDevicePreview() || isEditingTemplate();
      let {
        mode
      } = attributes;
      if (forcePreview) {
        mode = 'preview';
      }

      // Show toggle only for edit/preview modes and for blocks not in a query loop/FSE.
      let showToggle = blockType.supports.mode;
      if (mode === 'auto' || forcePreview) {
        showToggle = false;
      }

      // Configure toggle variables.
      const toggleText = mode === 'preview' ? acf.__('Switch to Edit') : acf.__('Switch to Preview');
      const toggleIcon = mode === 'preview' ? 'edit' : 'welcome-view-site';
      function toggleMode() {
        setAttributes({
          mode: mode === 'preview' ? 'edit' : 'preview'
        });
      }

      // Return template.
      return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(BlockControls, null, showToggle && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(ToolbarGroup, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(ToolbarButton, {
        className: "components-icon-button components-toolbar__control",
        label: toggleText,
        icon: toggleIcon,
        onClick: toggleMode
      }))), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(InspectorControls, null, mode === 'preview' && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)("div", {
        className: "acf-block-component acf-block-panel"
      }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(BlockForm, this.props))), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(BlockBody, this.props));
    }
  }

  /**
   * The BlockBody functional component.
   *
   * @date	19/2/19
   * @since	5.7.12
   */
  function _BlockBody(props) {
    const {
      attributes,
      isSelected,
      name
    } = props;
    const {
      mode
    } = attributes;
    let showForm = true;
    let additionalClasses = 'acf-block-component acf-block-body';
    if (mode === 'auto' && !isSelected || mode === 'preview') {
      additionalClasses += ' acf-block-preview';
      showForm = false;
    }
    if (getBlockVersion(name) > 1) {
      return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)("div", useBlockProps({
        className: additionalClasses
      }), showForm ? (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(BlockForm, props) : (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(BlockPreview, props));
    } else {
      return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)("div", useBlockProps(), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)("div", {
        className: "acf-block-component acf-block-body"
      }, showForm ? (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(BlockForm, props) : (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(BlockPreview, props)));
    }
  }

  // Append blockIndex to component props.
  const BlockBody = withSelect((select, ownProps) => {
    const {
      clientId
    } = ownProps;
    // Use optional rootClientId to allow discoverability of child blocks.
    const rootClientId = select('core/block-editor').getBlockRootClientId(clientId);
    const index = select('core/block-editor').getBlockIndex(clientId, rootClientId);
    return {
      index
    };
  })(_BlockBody);

  /**
   * A react component to append HTMl.
   *
   * @date	19/2/19
   * @since	5.7.12
   *
   * @param	string children The html to insert.
   * @return	void
   */
  class Div extends Component {
    render() {
      return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)("div", {
        dangerouslySetInnerHTML: {
          __html: this.props.children
        }
      });
    }
  }

  /**
   * A react Component for inline scripts.
   *
   * This Component uses a combination of React references and jQuery to append the
   * inline <script> HTML each time the component is rendered.
   *
   * @date	29/05/2020
   * @since	5.9.0
   *
   * @param	type Var Description.
   * @return	type Description.
   */
  class Script extends Component {
    render() {
      return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)("div", {
        ref: el => this.el = el
      });
    }
    setHTML(html) {
      $(this.el).html(`<script>${html}</script>`);
    }
    componentDidUpdate() {
      this.setHTML(this.props.children);
    }
    componentDidMount() {
      this.setHTML(this.props.children);
    }
  }

  // Data storage for DynamicHTML components.
  const store = {};

  /**
   * DynamicHTML Class.
   *
   * A react componenet to load and insert dynamic HTML.
   *
   * @date	19/2/19
   * @since	5.7.12
   *
   * @param	void
   * @return	void
   */
  class DynamicHTML extends Component {
    constructor(props) {
      super(props);

      // Bind callbacks.
      this.setRef = this.setRef.bind(this);

      // Define default props and call setup().
      this.id = '';
      this.el = false;
      this.subscribed = true;
      this.renderMethod = 'jQuery';
      this.setup(props);

      // Load state.
      this.loadState();
    }
    setup(props) {
      // Do nothing.
    }
    fetch() {
      // Do nothing.
    }
    maybePreload(blockId, clientId, form) {
      if (this.state.html === undefined && !isBlockInQueryLoop(this.props.clientId)) {
        const preloadedBlocks = acf.get('preloadedBlocks');
        const modeText = form ? 'form' : 'preview';
        if (preloadedBlocks && preloadedBlocks[blockId]) {
          // Ensure we only preload the correct block state (form or preview).
          if (form && !preloadedBlocks[blockId].form || !form && preloadedBlocks[blockId].form) return false;

          // Set HTML to the preloaded version.
          return preloadedBlocks[blockId].html.replaceAll(blockId, clientId);
        }
      }
      return false;
    }
    loadState() {
      this.state = store[this.id] || {};
    }
    setState(state) {
      store[this.id] = _objectSpread(_objectSpread({}, this.state), state);

      // Update component state if subscribed.
      // - Allows AJAX callback to update store without modifying state of an unmounted component.
      if (this.subscribed) {
        super.setState(state);
      }
    }
    setHtml(html) {
      html = html ? html.trim() : '';

      // Bail early if html has not changed.
      if (html === this.state.html) {
        return;
      }

      // Update state.
      const state = {
        html
      };
      if (this.renderMethod === 'jsx') {
        state.jsx = acf.parseJSX(html, getBlockVersion(this.props.name));

        // Handle templates which don't contain any valid JSX parsable elements.
        if (!state.jsx) {
          console.warn('Your ACF block template contains no valid HTML elements. Appending a empty div to prevent React JS errors.');
          state.html += '<div></div>';
          state.jsx = acf.parseJSX(state.html, getBlockVersion(this.props.name));
        }

        // If we've got an object (as an array) find the first valid React ref.
        if (Array.isArray(state.jsx)) {
          let refElement = state.jsx.find(element => React.isValidElement(element));
          state.ref = refElement.ref;
        } else {
          state.ref = state.jsx.ref;
        }
        state.$el = $(this.el);
      } else {
        state.$el = $(html);
      }
      this.setState(state);
    }
    setRef(el) {
      this.el = el;
    }
    render() {
      // Render JSX.
      if (this.state.jsx) {
        // If we're a v2+ block, use the jsx element itself as our ref.
        if (getBlockVersion(this.props.name) > 1) {
          this.setRef(this.state.jsx);
          return this.state.jsx;
        } else {
          return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)("div", {
            ref: this.setRef
          }, this.state.jsx);
        }
      }

      // Return HTML.
      return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)("div", {
        ref: this.setRef
      }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(Placeholder, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(Spinner, null)));
    }
    shouldComponentUpdate(_ref4, _ref5) {
      let {
        index
      } = _ref4;
      let {
        html
      } = _ref5;
      if (index !== this.props.index) {
        this.componentWillMove();
      }
      return html !== this.state.html;
    }
    display(context) {
      // This method is called after setting new HTML and the Component render.
      // The jQuery render method simply needs to move $el into place.
      if (this.renderMethod === 'jQuery') {
        const $el = this.state.$el;
        const $prevParent = $el.parent();
        const $thisParent = $(this.el);

        // Move $el into place.
        $thisParent.html($el);

        // Special case for reusable blocks.
        // Multiple instances of the same reusable block share the same block id.
        // This causes all instances to share the same state (cool), which unfortunately
        // pulls $el back and forth between the last rendered reusable block.
        // This simple fix leaves a "clone" behind :)
        if ($prevParent.length && $prevParent[0] !== $thisParent[0]) {
          $prevParent.html($el.clone());
        }
      }

      // Call context specific method.
      switch (context) {
        case 'append':
          this.componentDidAppend();
          break;
        case 'remount':
          this.componentDidRemount();
          break;
      }
    }
    componentDidMount() {
      // Fetch on first load.
      if (this.state.html === undefined) {
        this.fetch();

        // Or remount existing HTML.
      } else {
        this.display('remount');
      }
    }
    componentDidUpdate(prevProps, prevState) {
      // HTML has changed.
      this.display('append');
    }
    componentDidAppend() {
      acf.doAction('append', this.state.$el);
    }
    componentWillUnmount() {
      acf.doAction('unmount', this.state.$el);

      // Unsubscribe this component from state.
      this.subscribed = false;
    }
    componentDidRemount() {
      this.subscribed = true;

      // Use setTimeout to avoid incorrect timing of events.
      // React will unmount and mount components in DOM order.
      // This means a new component can be mounted before an old one is unmounted.
      // ACF shares $el across new/old components which is un-React-like.
      // This timout ensures that unmounting occurs before remounting.
      setTimeout(() => {
        acf.doAction('remount', this.state.$el);
      });
    }
    componentWillMove() {
      acf.doAction('unmount', this.state.$el);
      setTimeout(() => {
        acf.doAction('remount', this.state.$el);
      });
    }
  }

  /**
   * BlockForm Class.
   *
   * A react componenet to handle the block form.
   *
   * @date	19/2/19
   * @since	5.7.12
   *
   * @param	string id the block id.
   * @return	void
   */
  class BlockForm extends DynamicHTML {
    setup(_ref6) {
      let {
        clientId
      } = _ref6;
      this.id = `BlockForm-${clientId}`;
    }
    fetch() {
      // Extract props.
      const {
        attributes,
        context,
        clientId
      } = this.props;
      const hash = createBlockAttributesHash(attributes, context);

      // Try preloaded data first.
      const preloaded = this.maybePreload(hash, clientId, true);
      if (preloaded) {
        this.setHtml(preloaded);
        return;
      }

      // Request AJAX and update HTML on complete.
      fetchBlock({
        attributes,
        context,
        clientId,
        query: {
          form: true
        }
      }).done(_ref7 => {
        let {
          data
        } = _ref7;
        this.setHtml(data.form.replaceAll(data.clientId, clientId));
      });
    }
    componentDidRemount() {
      super.componentDidRemount();
      const {
        $el
      } = this.state;

      // Make sure our on append events are registered.
      if ($el.data('acf-events-added') !== true) {
        this.componentDidAppend();
      }
    }
    componentDidAppend() {
      super.componentDidAppend();

      // Extract props.
      const {
        attributes,
        setAttributes,
        clientId
      } = this.props;
      const props = this.props;
      const {
        $el
      } = this.state;

      // Callback for updating block data.
      function serializeData() {
        let silent = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
        const data = acf.serialize($el, `acf-${clientId}`);
        if (silent) {
          attributes.data = data;
        } else {
          setAttributes({
            data
          });
        }
      }

      // Add events.
      let timeout = false;
      $el.on('change keyup', () => {
        clearTimeout(timeout);
        timeout = setTimeout(serializeData, 300);
      });

      // Log initialization for remount check on the persistent element.
      $el.data('acf-events-added', true);

      // Ensure newly added block is saved with data.
      // Do it silently to avoid triggering a preview render.
      if (!attributes.data) {
        serializeData(true);
      }
    }
  }

  /**
   * BlockPreview Class.
   *
   * A react componenet to handle the block preview.
   *
   * @date	19/2/19
   * @since	5.7.12
   *
   * @param	string id the block id.
   * @return	void
   */
  class BlockPreview extends DynamicHTML {
    setup(_ref8) {
      let {
        clientId,
        name
      } = _ref8;
      const blockType = getBlockType(name);
      const contextPostId = acf.isget(this.props, 'context', 'postId');
      this.id = `BlockPreview-${clientId}`;

      // Apply the contextPostId to the ID if set to stop query loop ID duplication.
      if (contextPostId) {
        this.id = `BlockPreview-${clientId}-${contextPostId}`;
      }
      if (blockType.supports.jsx) {
        this.renderMethod = 'jsx';
      }
    }
    fetch() {
      let args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      const {
        attributes = this.props.attributes,
        clientId = this.props.clientId,
        context = this.props.context,
        delay = 0
      } = args;
      const {
        name
      } = this.props;

      // Remember attributes used to fetch HTML.
      this.setState({
        prevAttributes: attributes,
        prevContext: context
      });
      const hash = createBlockAttributesHash(attributes, context);

      // Try preloaded data first.
      let preloaded = this.maybePreload(hash, clientId, false);
      if (preloaded) {
        if (getBlockVersion(name) == 1) {
          preloaded = '<div class="acf-block-preview">' + preloaded + '</div>';
        }
        this.setHtml(preloaded);
        return;
      }

      // Request AJAX and update HTML on complete.
      fetchBlock({
        attributes,
        context,
        clientId,
        query: {
          preview: true
        },
        delay
      }).done(_ref9 => {
        let {
          data
        } = _ref9;
        let replaceHtml = data.preview.replaceAll(data.clientId, clientId);
        if (getBlockVersion(name) == 1) {
          replaceHtml = '<div class="acf-block-preview">' + replaceHtml + '</div>';
        }
        this.setHtml(replaceHtml);
      });
    }
    componentDidAppend() {
      super.componentDidAppend();
      this.renderBlockPreviewEvent();
    }
    shouldComponentUpdate(nextProps, nextState) {
      const nextAttributes = nextProps.attributes;
      const thisAttributes = this.props.attributes;

      // Update preview if block data has changed.
      if (!compareObjects(nextAttributes, thisAttributes) || !compareObjects(nextProps.context, this.props.context)) {
        let delay = 0;

        // Delay fetch when editing className or anchor to simulate consistent logic to custom fields.
        if (nextAttributes.className !== thisAttributes.className) {
          delay = 300;
        }
        if (nextAttributes.anchor !== thisAttributes.anchor) {
          delay = 300;
        }
        this.fetch({
          attributes: nextAttributes,
          context: nextProps.context,
          delay
        });
      }
      return super.shouldComponentUpdate(nextProps, nextState);
    }
    renderBlockPreviewEvent() {
      // Extract props.
      const {
        attributes,
        name
      } = this.props;
      const {
        $el,
        ref
      } = this.state;
      var blockElement;

      // Generate action friendly type.
      const type = attributes.name.replace('acf/', '');
      if (ref && ref.current) {
        // We've got a react ref from a JSX container. Use the parent as the blockElement
        blockElement = $(ref.current).parent();
      } else if (getBlockVersion(name) == 1) {
        blockElement = $el;
      } else {
        blockElement = $el.parents('.acf-block-preview');
      }

      // Do action.
      acf.doAction('render_block_preview', blockElement, attributes);
      acf.doAction(`render_block_preview/type=${type}`, blockElement, attributes);
    }
    componentDidRemount() {
      super.componentDidRemount();

      // Update preview if data has changed since last render (changing from "edit" to "preview").
      if (!compareObjects(this.state.prevAttributes, this.props.attributes) || !compareObjects(this.state.prevContext, this.props.context)) {
        this.fetch();
      }

      // Fire the block preview event so blocks can reinit JS elements.
      // React reusing DOM elements covers any potential race condition from the above fetch.
      this.renderBlockPreviewEvent();
    }
  }

  /**
   * Initializes ACF Blocks logic and registration.
   *
   * @since 5.9.0
   */
  function initialize() {
    // Add support for WordPress versions before 5.2.
    if (!wp.blockEditor) {
      wp.blockEditor = wp.editor;
    }

    // Register block types.
    const blockTypes = acf.get('blockTypes');
    if (blockTypes) {
      blockTypes.map(registerBlockType);
    }
  }

  // Run the initialize callback during the "prepare" action.
  // This ensures that all localized data is available and that blocks are registered before the WP editor has been instantiated.
  acf.addAction('prepare', initialize);

  /**
   * Returns a valid vertical alignment.
   *
   * @date	07/08/2020
   * @since	5.9.0
   *
   * @param	string align A vertical alignment.
   * @return	string
   */
  function validateVerticalAlignment(align) {
    const ALIGNMENTS = ['top', 'center', 'bottom'];
    const DEFAULT = 'top';
    return ALIGNMENTS.includes(align) ? align : DEFAULT;
  }

  /**
   * Returns a valid horizontal alignment.
   *
   * @date	07/08/2020
   * @since	5.9.0
   *
   * @param	string align A horizontal alignment.
   * @return	string
   */
  function validateHorizontalAlignment(align) {
    const ALIGNMENTS = ['left', 'center', 'right'];
    const DEFAULT = acf.get('rtl') ? 'right' : 'left';
    return ALIGNMENTS.includes(align) ? align : DEFAULT;
  }

  /**
   * Returns a valid matrix alignment.
   *
   * Written for "upgrade-path" compatibility from vertical alignment to matrix alignment.
   *
   * @date	07/08/2020
   * @since	5.9.0
   *
   * @param	string align A matrix alignment.
   * @return	string
   */
  function validateMatrixAlignment(align) {
    const DEFAULT = 'center center';
    if (align) {
      const [y, x] = align.split(' ');
      return `${validateVerticalAlignment(y)} ${validateHorizontalAlignment(x)}`;
    }
    return DEFAULT;
  }

  /**
   * A higher order component adding alignContent editing functionality.
   *
   * @date	08/07/2020
   * @since	5.9.0
   *
   * @param	component OriginalBlockEdit The original BlockEdit component.
   * @param	object blockType The block type settings.
   * @return	component
   */
  function withAlignContentComponent(OriginalBlockEdit, blockType) {
    // Determine alignment vars
    let type = blockType.supports.align_content || blockType.supports.alignContent;
    let AlignmentComponent;
    let validateAlignment;
    switch (type) {
      case 'matrix':
        AlignmentComponent = BlockAlignmentMatrixControl || BlockAlignmentMatrixToolbar;
        validateAlignment = validateMatrixAlignment;
        break;
      default:
        AlignmentComponent = BlockVerticalAlignmentToolbar;
        validateAlignment = validateVerticalAlignment;
        break;
    }

    // Ensure alignment component exists.
    if (AlignmentComponent === undefined) {
      console.warn(`The "${type}" alignment component was not found.`);
      return OriginalBlockEdit;
    }

    // Ensure correct block attribute data is sent in intial preview AJAX request.
    blockType.alignContent = validateAlignment(blockType.alignContent);

    // Return wrapped component.
    return class WrappedBlockEdit extends Component {
      render() {
        const {
          attributes,
          setAttributes
        } = this.props;
        const {
          alignContent
        } = attributes;
        function onChangeAlignContent(alignContent) {
          setAttributes({
            alignContent: validateAlignment(alignContent)
          });
        }
        return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(BlockControls, {
          group: "block"
        }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(AlignmentComponent, {
          label: acf.__('Change content alignment'),
          value: validateAlignment(alignContent),
          onChange: onChangeAlignContent
        })), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(OriginalBlockEdit, this.props));
      }
    };
  }

  /**
   * A higher order component adding alignText editing functionality.
   *
   * @date	08/07/2020
   * @since	5.9.0
   *
   * @param	component OriginalBlockEdit The original BlockEdit component.
   * @param	object blockType The block type settings.
   * @return	component
   */
  function withAlignTextComponent(OriginalBlockEdit, blockType) {
    const validateAlignment = validateHorizontalAlignment;

    // Ensure correct block attribute data is sent in intial preview AJAX request.
    blockType.alignText = validateAlignment(blockType.alignText);

    // Return wrapped component.
    return class WrappedBlockEdit extends Component {
      render() {
        const {
          attributes,
          setAttributes
        } = this.props;
        const {
          alignText
        } = attributes;
        function onChangeAlignText(alignText) {
          setAttributes({
            alignText: validateAlignment(alignText)
          });
        }
        return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(BlockControls, {
          group: "block"
        }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(AlignmentToolbar, {
          value: validateAlignment(alignText),
          onChange: onChangeAlignText
        })), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(OriginalBlockEdit, this.props));
      }
    };
  }

  /**
   * A higher order component adding full height support.
   *
   * @date	19/07/2021
   * @since	5.10.0
   *
   * @param	component OriginalBlockEdit The original BlockEdit component.
   * @param	object blockType The block type settings.
   * @return	component
   */
  function withFullHeightComponent(OriginalBlockEdit, blockType) {
    if (!BlockFullHeightAlignmentControl) return OriginalBlockEdit;

    // Return wrapped component.
    return class WrappedBlockEdit extends Component {
      render() {
        const {
          attributes,
          setAttributes
        } = this.props;
        const {
          fullHeight
        } = attributes;
        function onToggleFullHeight(fullHeight) {
          setAttributes({
            fullHeight
          });
        }
        return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(BlockControls, {
          group: "block"
        }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(BlockFullHeightAlignmentControl, {
          isActive: fullHeight,
          onToggle: onToggleFullHeight
        })), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(OriginalBlockEdit, this.props));
      }
    };
  }

  /**
   * Appends a backwards compatibility attribute for conversion.
   *
   * @since	6.0
   *
   * @param	object attributes The block type attributes.
   * @return	object
   */
  function addBackCompatAttribute(attributes, new_attribute, type) {
    attributes[new_attribute] = {
      type: type
    };
    return attributes;
  }

  /**
   * Create a block hash from attributes
   *
   * @since 6.0
   *
   * @param object attributes The block type attributes.
   * @param object context The current block context object.
   * @return string
   */
  function createBlockAttributesHash(attributes, context) {
    attributes['_acf_context'] = context;
    return md5(JSON.stringify(Object.keys(attributes).sort().reduce((acc, currValue) => {
      acc[currValue] = attributes[currValue];
      return acc;
    }, {})));
  }
})(jQuery);

/***/ }),

/***/ "./src/advanced-custom-fields-pro/assets/src/js/pro/_acf-jsx-names.js":
/*!****************************************************************************!*\
  !*** ./src/advanced-custom-fields-pro/assets/src/js/pro/_acf-jsx-names.js ***!
  \****************************************************************************/
/***/ (() => {

(function ($, undefined) {
  acf.jsxNameReplacements = {
    'accent-height': 'accentHeight',
    accentheight: 'accentHeight',
    'accept-charset': 'acceptCharset',
    acceptcharset: 'acceptCharset',
    accesskey: 'accessKey',
    'alignment-baseline': 'alignmentBaseline',
    alignmentbaseline: 'alignmentBaseline',
    allowedblocks: 'allowedBlocks',
    allowfullscreen: 'allowFullScreen',
    allowreorder: 'allowReorder',
    'arabic-form': 'arabicForm',
    arabicform: 'arabicForm',
    attributename: 'attributeName',
    attributetype: 'attributeType',
    autocapitalize: 'autoCapitalize',
    autocomplete: 'autoComplete',
    autocorrect: 'autoCorrect',
    autofocus: 'autoFocus',
    autoplay: 'autoPlay',
    autoreverse: 'autoReverse',
    autosave: 'autoSave',
    basefrequency: 'baseFrequency',
    'baseline-shift': 'baselineShift',
    baselineshift: 'baselineShift',
    baseprofile: 'baseProfile',
    calcmode: 'calcMode',
    'cap-height': 'capHeight',
    capheight: 'capHeight',
    cellpadding: 'cellPadding',
    cellspacing: 'cellSpacing',
    charset: 'charSet',
    class: 'className',
    classid: 'classID',
    classname: 'className',
    'clip-path': 'clipPath',
    'clip-rule': 'clipRule',
    clippath: 'clipPath',
    clippathunits: 'clipPathUnits',
    cliprule: 'clipRule',
    'color-interpolation': 'colorInterpolation',
    'color-interpolation-filters': 'colorInterpolationFilters',
    'color-profile': 'colorProfile',
    'color-rendering': 'colorRendering',
    colorinterpolation: 'colorInterpolation',
    colorinterpolationfilters: 'colorInterpolationFilters',
    colorprofile: 'colorProfile',
    colorrendering: 'colorRendering',
    colspan: 'colSpan',
    contenteditable: 'contentEditable',
    contentscripttype: 'contentScriptType',
    contentstyletype: 'contentStyleType',
    contextmenu: 'contextMenu',
    controlslist: 'controlsList',
    crossorigin: 'crossOrigin',
    dangerouslysetinnerhtml: 'dangerouslySetInnerHTML',
    datetime: 'dateTime',
    defaultchecked: 'defaultChecked',
    defaultvalue: 'defaultValue',
    diffuseconstant: 'diffuseConstant',
    disablepictureinpicture: 'disablePictureInPicture',
    disableremoteplayback: 'disableRemotePlayback',
    'dominant-baseline': 'dominantBaseline',
    dominantbaseline: 'dominantBaseline',
    edgemode: 'edgeMode',
    'enable-background': 'enableBackground',
    enablebackground: 'enableBackground',
    enctype: 'encType',
    enterkeyhint: 'enterKeyHint',
    externalresourcesrequired: 'externalResourcesRequired',
    'fill-opacity': 'fillOpacity',
    'fill-rule': 'fillRule',
    fillopacity: 'fillOpacity',
    fillrule: 'fillRule',
    filterres: 'filterRes',
    filterunits: 'filterUnits',
    'flood-color': 'floodColor',
    'flood-opacity': 'floodOpacity',
    floodcolor: 'floodColor',
    floodopacity: 'floodOpacity',
    'font-family': 'fontFamily',
    'font-size': 'fontSize',
    'font-size-adjust': 'fontSizeAdjust',
    'font-stretch': 'fontStretch',
    'font-style': 'fontStyle',
    'font-variant': 'fontVariant',
    'font-weight': 'fontWeight',
    fontfamily: 'fontFamily',
    fontsize: 'fontSize',
    fontsizeadjust: 'fontSizeAdjust',
    fontstretch: 'fontStretch',
    fontstyle: 'fontStyle',
    fontvariant: 'fontVariant',
    fontweight: 'fontWeight',
    for: 'htmlFor',
    foreignobject: 'foreignObject',
    formaction: 'formAction',
    formenctype: 'formEncType',
    formmethod: 'formMethod',
    formnovalidate: 'formNoValidate',
    formtarget: 'formTarget',
    frameborder: 'frameBorder',
    'glyph-name': 'glyphName',
    'glyph-orientation-horizontal': 'glyphOrientationHorizontal',
    'glyph-orientation-vertical': 'glyphOrientationVertical',
    glyphname: 'glyphName',
    glyphorientationhorizontal: 'glyphOrientationHorizontal',
    glyphorientationvertical: 'glyphOrientationVertical',
    glyphref: 'glyphRef',
    gradienttransform: 'gradientTransform',
    gradientunits: 'gradientUnits',
    'horiz-adv-x': 'horizAdvX',
    'horiz-origin-x': 'horizOriginX',
    horizadvx: 'horizAdvX',
    horizoriginx: 'horizOriginX',
    hreflang: 'hrefLang',
    htmlfor: 'htmlFor',
    'http-equiv': 'httpEquiv',
    httpequiv: 'httpEquiv',
    'image-rendering': 'imageRendering',
    imagerendering: 'imageRendering',
    innerhtml: 'innerHTML',
    inputmode: 'inputMode',
    itemid: 'itemID',
    itemprop: 'itemProp',
    itemref: 'itemRef',
    itemscope: 'itemScope',
    itemtype: 'itemType',
    kernelmatrix: 'kernelMatrix',
    kernelunitlength: 'kernelUnitLength',
    keyparams: 'keyParams',
    keypoints: 'keyPoints',
    keysplines: 'keySplines',
    keytimes: 'keyTimes',
    keytype: 'keyType',
    lengthadjust: 'lengthAdjust',
    'letter-spacing': 'letterSpacing',
    letterspacing: 'letterSpacing',
    'lighting-color': 'lightingColor',
    lightingcolor: 'lightingColor',
    limitingconeangle: 'limitingConeAngle',
    marginheight: 'marginHeight',
    marginwidth: 'marginWidth',
    'marker-end': 'markerEnd',
    'marker-mid': 'markerMid',
    'marker-start': 'markerStart',
    markerend: 'markerEnd',
    markerheight: 'markerHeight',
    markermid: 'markerMid',
    markerstart: 'markerStart',
    markerunits: 'markerUnits',
    markerwidth: 'markerWidth',
    maskcontentunits: 'maskContentUnits',
    maskunits: 'maskUnits',
    maxlength: 'maxLength',
    mediagroup: 'mediaGroup',
    minlength: 'minLength',
    nomodule: 'noModule',
    novalidate: 'noValidate',
    numoctaves: 'numOctaves',
    'overline-position': 'overlinePosition',
    'overline-thickness': 'overlineThickness',
    overlineposition: 'overlinePosition',
    overlinethickness: 'overlineThickness',
    'paint-order': 'paintOrder',
    paintorder: 'paintOrder',
    'panose-1': 'panose1',
    pathlength: 'pathLength',
    patterncontentunits: 'patternContentUnits',
    patterntransform: 'patternTransform',
    patternunits: 'patternUnits',
    playsinline: 'playsInline',
    'pointer-events': 'pointerEvents',
    pointerevents: 'pointerEvents',
    pointsatx: 'pointsAtX',
    pointsaty: 'pointsAtY',
    pointsatz: 'pointsAtZ',
    preservealpha: 'preserveAlpha',
    preserveaspectratio: 'preserveAspectRatio',
    primitiveunits: 'primitiveUnits',
    radiogroup: 'radioGroup',
    readonly: 'readOnly',
    referrerpolicy: 'referrerPolicy',
    refx: 'refX',
    refy: 'refY',
    'rendering-intent': 'renderingIntent',
    renderingintent: 'renderingIntent',
    repeatcount: 'repeatCount',
    repeatdur: 'repeatDur',
    requiredextensions: 'requiredExtensions',
    requiredfeatures: 'requiredFeatures',
    rowspan: 'rowSpan',
    'shape-rendering': 'shapeRendering',
    shaperendering: 'shapeRendering',
    specularconstant: 'specularConstant',
    specularexponent: 'specularExponent',
    spellcheck: 'spellCheck',
    spreadmethod: 'spreadMethod',
    srcdoc: 'srcDoc',
    srclang: 'srcLang',
    srcset: 'srcSet',
    startoffset: 'startOffset',
    stddeviation: 'stdDeviation',
    stitchtiles: 'stitchTiles',
    'stop-color': 'stopColor',
    'stop-opacity': 'stopOpacity',
    stopcolor: 'stopColor',
    stopopacity: 'stopOpacity',
    'strikethrough-position': 'strikethroughPosition',
    'strikethrough-thickness': 'strikethroughThickness',
    strikethroughposition: 'strikethroughPosition',
    strikethroughthickness: 'strikethroughThickness',
    'stroke-dasharray': 'strokeDasharray',
    'stroke-dashoffset': 'strokeDashoffset',
    'stroke-linecap': 'strokeLinecap',
    'stroke-linejoin': 'strokeLinejoin',
    'stroke-miterlimit': 'strokeMiterlimit',
    'stroke-opacity': 'strokeOpacity',
    'stroke-width': 'strokeWidth',
    strokedasharray: 'strokeDasharray',
    strokedashoffset: 'strokeDashoffset',
    strokelinecap: 'strokeLinecap',
    strokelinejoin: 'strokeLinejoin',
    strokemiterlimit: 'strokeMiterlimit',
    strokeopacity: 'strokeOpacity',
    strokewidth: 'strokeWidth',
    suppresscontenteditablewarning: 'suppressContentEditableWarning',
    suppresshydrationwarning: 'suppressHydrationWarning',
    surfacescale: 'surfaceScale',
    systemlanguage: 'systemLanguage',
    tabindex: 'tabIndex',
    tablevalues: 'tableValues',
    targetx: 'targetX',
    targety: 'targetY',
    templatelock: 'templateLock',
    'text-anchor': 'textAnchor',
    'text-decoration': 'textDecoration',
    'text-rendering': 'textRendering',
    textanchor: 'textAnchor',
    textdecoration: 'textDecoration',
    textlength: 'textLength',
    textrendering: 'textRendering',
    'underline-position': 'underlinePosition',
    'underline-thickness': 'underlineThickness',
    underlineposition: 'underlinePosition',
    underlinethickness: 'underlineThickness',
    'unicode-bidi': 'unicodeBidi',
    'unicode-range': 'unicodeRange',
    unicodebidi: 'unicodeBidi',
    unicoderange: 'unicodeRange',
    'units-per-em': 'unitsPerEm',
    unitsperem: 'unitsPerEm',
    usemap: 'useMap',
    'v-alphabetic': 'vAlphabetic',
    'v-hanging': 'vHanging',
    'v-ideographic': 'vIdeographic',
    'v-mathematical': 'vMathematical',
    valphabetic: 'vAlphabetic',
    'vector-effect': 'vectorEffect',
    vectoreffect: 'vectorEffect',
    'vert-adv-y': 'vertAdvY',
    'vert-origin-x': 'vertOriginX',
    'vert-origin-y': 'vertOriginY',
    vertadvy: 'vertAdvY',
    vertoriginx: 'vertOriginX',
    vertoriginy: 'vertOriginY',
    vhanging: 'vHanging',
    videographic: 'vIdeographic',
    viewbox: 'viewBox',
    viewtarget: 'viewTarget',
    vmathematical: 'vMathematical',
    'word-spacing': 'wordSpacing',
    wordspacing: 'wordSpacing',
    'writing-mode': 'writingMode',
    writingmode: 'writingMode',
    'x-height': 'xHeight',
    xchannelselector: 'xChannelSelector',
    xheight: 'xHeight',
    'xlink:actuate': 'xlinkActuate',
    'xlink:arcrole': 'xlinkArcrole',
    'xlink:href': 'xlinkHref',
    'xlink:role': 'xlinkRole',
    'xlink:show': 'xlinkShow',
    'xlink:title': 'xlinkTitle',
    'xlink:type': 'xlinkType',
    xlinkactuate: 'xlinkActuate',
    xlinkarcrole: 'xlinkArcrole',
    xlinkhref: 'xlinkHref',
    xlinkrole: 'xlinkRole',
    xlinkshow: 'xlinkShow',
    xlinktitle: 'xlinkTitle',
    xlinktype: 'xlinkType',
    'xml:base': 'xmlBase',
    'xml:lang': 'xmlLang',
    'xml:space': 'xmlSpace',
    xmlbase: 'xmlBase',
    xmllang: 'xmlLang',
    'xmlns:xlink': 'xmlnsXlink',
    xmlnsxlink: 'xmlnsXlink',
    xmlspace: 'xmlSpace',
    ychannelselector: 'yChannelSelector',
    zoomandpan: 'zoomAndPan'
  };
})(jQuery);

/***/ }),

/***/ "./node_modules/charenc/charenc.js":
/*!*****************************************!*\
  !*** ./node_modules/charenc/charenc.js ***!
  \*****************************************/
/***/ ((module) => {

var charenc = {
  // UTF-8 encoding
  utf8: {
    // Convert a string to a byte array
    stringToBytes: function(str) {
      return charenc.bin.stringToBytes(unescape(encodeURIComponent(str)));
    },

    // Convert a byte array to a string
    bytesToString: function(bytes) {
      return decodeURIComponent(escape(charenc.bin.bytesToString(bytes)));
    }
  },

  // Binary encoding
  bin: {
    // Convert a string to a byte array
    stringToBytes: function(str) {
      for (var bytes = [], i = 0; i < str.length; i++)
        bytes.push(str.charCodeAt(i) & 0xFF);
      return bytes;
    },

    // Convert a byte array to a string
    bytesToString: function(bytes) {
      for (var str = [], i = 0; i < bytes.length; i++)
        str.push(String.fromCharCode(bytes[i]));
      return str.join('');
    }
  }
};

module.exports = charenc;


/***/ }),

/***/ "./node_modules/crypt/crypt.js":
/*!*************************************!*\
  !*** ./node_modules/crypt/crypt.js ***!
  \*************************************/
/***/ ((module) => {

(function() {
  var base64map
      = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/',

  crypt = {
    // Bit-wise rotation left
    rotl: function(n, b) {
      return (n << b) | (n >>> (32 - b));
    },

    // Bit-wise rotation right
    rotr: function(n, b) {
      return (n << (32 - b)) | (n >>> b);
    },

    // Swap big-endian to little-endian and vice versa
    endian: function(n) {
      // If number given, swap endian
      if (n.constructor == Number) {
        return crypt.rotl(n, 8) & 0x00FF00FF | crypt.rotl(n, 24) & 0xFF00FF00;
      }

      // Else, assume array and swap all items
      for (var i = 0; i < n.length; i++)
        n[i] = crypt.endian(n[i]);
      return n;
    },

    // Generate an array of any length of random bytes
    randomBytes: function(n) {
      for (var bytes = []; n > 0; n--)
        bytes.push(Math.floor(Math.random() * 256));
      return bytes;
    },

    // Convert a byte array to big-endian 32-bit words
    bytesToWords: function(bytes) {
      for (var words = [], i = 0, b = 0; i < bytes.length; i++, b += 8)
        words[b >>> 5] |= bytes[i] << (24 - b % 32);
      return words;
    },

    // Convert big-endian 32-bit words to a byte array
    wordsToBytes: function(words) {
      for (var bytes = [], b = 0; b < words.length * 32; b += 8)
        bytes.push((words[b >>> 5] >>> (24 - b % 32)) & 0xFF);
      return bytes;
    },

    // Convert a byte array to a hex string
    bytesToHex: function(bytes) {
      for (var hex = [], i = 0; i < bytes.length; i++) {
        hex.push((bytes[i] >>> 4).toString(16));
        hex.push((bytes[i] & 0xF).toString(16));
      }
      return hex.join('');
    },

    // Convert a hex string to a byte array
    hexToBytes: function(hex) {
      for (var bytes = [], c = 0; c < hex.length; c += 2)
        bytes.push(parseInt(hex.substr(c, 2), 16));
      return bytes;
    },

    // Convert a byte array to a base-64 string
    bytesToBase64: function(bytes) {
      for (var base64 = [], i = 0; i < bytes.length; i += 3) {
        var triplet = (bytes[i] << 16) | (bytes[i + 1] << 8) | bytes[i + 2];
        for (var j = 0; j < 4; j++)
          if (i * 8 + j * 6 <= bytes.length * 8)
            base64.push(base64map.charAt((triplet >>> 6 * (3 - j)) & 0x3F));
          else
            base64.push('=');
      }
      return base64.join('');
    },

    // Convert a base-64 string to a byte array
    base64ToBytes: function(base64) {
      // Remove non-base-64 characters
      base64 = base64.replace(/[^A-Z0-9+\/]/ig, '');

      for (var bytes = [], i = 0, imod4 = 0; i < base64.length;
          imod4 = ++i % 4) {
        if (imod4 == 0) continue;
        bytes.push(((base64map.indexOf(base64.charAt(i - 1))
            & (Math.pow(2, -2 * imod4 + 8) - 1)) << (imod4 * 2))
            | (base64map.indexOf(base64.charAt(i)) >>> (6 - imod4 * 2)));
      }
      return bytes;
    }
  };

  module.exports = crypt;
})();


/***/ }),

/***/ "./node_modules/is-buffer/index.js":
/*!*****************************************!*\
  !*** ./node_modules/is-buffer/index.js ***!
  \*****************************************/
/***/ ((module) => {

/*!
 * Determine if an object is a Buffer
 *
 * @author   Feross Aboukhadijeh <https://feross.org>
 * @license  MIT
 */

// The _isBuffer check is for Safari 5-7 support, because it's missing
// Object.prototype.constructor. Remove this eventually
module.exports = function (obj) {
  return obj != null && (isBuffer(obj) || isSlowBuffer(obj) || !!obj._isBuffer)
}

function isBuffer (obj) {
  return !!obj.constructor && typeof obj.constructor.isBuffer === 'function' && obj.constructor.isBuffer(obj)
}

// For Node v0.10 support. Remove this eventually.
function isSlowBuffer (obj) {
  return typeof obj.readFloatLE === 'function' && typeof obj.slice === 'function' && isBuffer(obj.slice(0, 0))
}


/***/ }),

/***/ "./node_modules/md5/md5.js":
/*!*********************************!*\
  !*** ./node_modules/md5/md5.js ***!
  \*********************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

(function(){
  var crypt = __webpack_require__(/*! crypt */ "./node_modules/crypt/crypt.js"),
      utf8 = (__webpack_require__(/*! charenc */ "./node_modules/charenc/charenc.js").utf8),
      isBuffer = __webpack_require__(/*! is-buffer */ "./node_modules/is-buffer/index.js"),
      bin = (__webpack_require__(/*! charenc */ "./node_modules/charenc/charenc.js").bin),

  // The core
  md5 = function (message, options) {
    // Convert to byte array
    if (message.constructor == String)
      if (options && options.encoding === 'binary')
        message = bin.stringToBytes(message);
      else
        message = utf8.stringToBytes(message);
    else if (isBuffer(message))
      message = Array.prototype.slice.call(message, 0);
    else if (!Array.isArray(message) && message.constructor !== Uint8Array)
      message = message.toString();
    // else, assume byte array already

    var m = crypt.bytesToWords(message),
        l = message.length * 8,
        a =  1732584193,
        b = -271733879,
        c = -1732584194,
        d =  271733878;

    // Swap endian
    for (var i = 0; i < m.length; i++) {
      m[i] = ((m[i] <<  8) | (m[i] >>> 24)) & 0x00FF00FF |
             ((m[i] << 24) | (m[i] >>>  8)) & 0xFF00FF00;
    }

    // Padding
    m[l >>> 5] |= 0x80 << (l % 32);
    m[(((l + 64) >>> 9) << 4) + 14] = l;

    // Method shortcuts
    var FF = md5._ff,
        GG = md5._gg,
        HH = md5._hh,
        II = md5._ii;

    for (var i = 0; i < m.length; i += 16) {

      var aa = a,
          bb = b,
          cc = c,
          dd = d;

      a = FF(a, b, c, d, m[i+ 0],  7, -680876936);
      d = FF(d, a, b, c, m[i+ 1], 12, -389564586);
      c = FF(c, d, a, b, m[i+ 2], 17,  606105819);
      b = FF(b, c, d, a, m[i+ 3], 22, -1044525330);
      a = FF(a, b, c, d, m[i+ 4],  7, -176418897);
      d = FF(d, a, b, c, m[i+ 5], 12,  1200080426);
      c = FF(c, d, a, b, m[i+ 6], 17, -1473231341);
      b = FF(b, c, d, a, m[i+ 7], 22, -45705983);
      a = FF(a, b, c, d, m[i+ 8],  7,  1770035416);
      d = FF(d, a, b, c, m[i+ 9], 12, -1958414417);
      c = FF(c, d, a, b, m[i+10], 17, -42063);
      b = FF(b, c, d, a, m[i+11], 22, -1990404162);
      a = FF(a, b, c, d, m[i+12],  7,  1804603682);
      d = FF(d, a, b, c, m[i+13], 12, -40341101);
      c = FF(c, d, a, b, m[i+14], 17, -1502002290);
      b = FF(b, c, d, a, m[i+15], 22,  1236535329);

      a = GG(a, b, c, d, m[i+ 1],  5, -165796510);
      d = GG(d, a, b, c, m[i+ 6],  9, -1069501632);
      c = GG(c, d, a, b, m[i+11], 14,  643717713);
      b = GG(b, c, d, a, m[i+ 0], 20, -373897302);
      a = GG(a, b, c, d, m[i+ 5],  5, -701558691);
      d = GG(d, a, b, c, m[i+10],  9,  38016083);
      c = GG(c, d, a, b, m[i+15], 14, -660478335);
      b = GG(b, c, d, a, m[i+ 4], 20, -405537848);
      a = GG(a, b, c, d, m[i+ 9],  5,  568446438);
      d = GG(d, a, b, c, m[i+14],  9, -1019803690);
      c = GG(c, d, a, b, m[i+ 3], 14, -187363961);
      b = GG(b, c, d, a, m[i+ 8], 20,  1163531501);
      a = GG(a, b, c, d, m[i+13],  5, -1444681467);
      d = GG(d, a, b, c, m[i+ 2],  9, -51403784);
      c = GG(c, d, a, b, m[i+ 7], 14,  1735328473);
      b = GG(b, c, d, a, m[i+12], 20, -1926607734);

      a = HH(a, b, c, d, m[i+ 5],  4, -378558);
      d = HH(d, a, b, c, m[i+ 8], 11, -2022574463);
      c = HH(c, d, a, b, m[i+11], 16,  1839030562);
      b = HH(b, c, d, a, m[i+14], 23, -35309556);
      a = HH(a, b, c, d, m[i+ 1],  4, -1530992060);
      d = HH(d, a, b, c, m[i+ 4], 11,  1272893353);
      c = HH(c, d, a, b, m[i+ 7], 16, -155497632);
      b = HH(b, c, d, a, m[i+10], 23, -1094730640);
      a = HH(a, b, c, d, m[i+13],  4,  681279174);
      d = HH(d, a, b, c, m[i+ 0], 11, -358537222);
      c = HH(c, d, a, b, m[i+ 3], 16, -722521979);
      b = HH(b, c, d, a, m[i+ 6], 23,  76029189);
      a = HH(a, b, c, d, m[i+ 9],  4, -640364487);
      d = HH(d, a, b, c, m[i+12], 11, -421815835);
      c = HH(c, d, a, b, m[i+15], 16,  530742520);
      b = HH(b, c, d, a, m[i+ 2], 23, -995338651);

      a = II(a, b, c, d, m[i+ 0],  6, -198630844);
      d = II(d, a, b, c, m[i+ 7], 10,  1126891415);
      c = II(c, d, a, b, m[i+14], 15, -1416354905);
      b = II(b, c, d, a, m[i+ 5], 21, -57434055);
      a = II(a, b, c, d, m[i+12],  6,  1700485571);
      d = II(d, a, b, c, m[i+ 3], 10, -1894986606);
      c = II(c, d, a, b, m[i+10], 15, -1051523);
      b = II(b, c, d, a, m[i+ 1], 21, -2054922799);
      a = II(a, b, c, d, m[i+ 8],  6,  1873313359);
      d = II(d, a, b, c, m[i+15], 10, -30611744);
      c = II(c, d, a, b, m[i+ 6], 15, -1560198380);
      b = II(b, c, d, a, m[i+13], 21,  1309151649);
      a = II(a, b, c, d, m[i+ 4],  6, -145523070);
      d = II(d, a, b, c, m[i+11], 10, -1120210379);
      c = II(c, d, a, b, m[i+ 2], 15,  718787259);
      b = II(b, c, d, a, m[i+ 9], 21, -343485551);

      a = (a + aa) >>> 0;
      b = (b + bb) >>> 0;
      c = (c + cc) >>> 0;
      d = (d + dd) >>> 0;
    }

    return crypt.endian([a, b, c, d]);
  };

  // Auxiliary functions
  md5._ff  = function (a, b, c, d, x, s, t) {
    var n = a + (b & c | ~b & d) + (x >>> 0) + t;
    return ((n << s) | (n >>> (32 - s))) + b;
  };
  md5._gg  = function (a, b, c, d, x, s, t) {
    var n = a + (b & d | c & ~d) + (x >>> 0) + t;
    return ((n << s) | (n >>> (32 - s))) + b;
  };
  md5._hh  = function (a, b, c, d, x, s, t) {
    var n = a + (b ^ c ^ d) + (x >>> 0) + t;
    return ((n << s) | (n >>> (32 - s))) + b;
  };
  md5._ii  = function (a, b, c, d, x, s, t) {
    var n = a + (c ^ (b | ~d)) + (x >>> 0) + t;
    return ((n << s) | (n >>> (32 - s))) + b;
  };

  // Package private blocksize
  md5._blocksize = 16;
  md5._digestsize = 16;

  module.exports = function (message, options) {
    if (message === undefined || message === null)
      throw new Error('Illegal argument ' + message);

    var digestbytes = crypt.wordsToBytes(md5(message, options));
    return options && options.asBytes ? digestbytes :
        options && options.asString ? bin.bytesToString(digestbytes) :
        crypt.bytesToHex(digestbytes);
  };

})();


/***/ }),

/***/ "./node_modules/object-assign/index.js":
/*!*********************************************!*\
  !*** ./node_modules/object-assign/index.js ***!
  \*********************************************/
/***/ ((module) => {

"use strict";
/*
object-assign
(c) Sindre Sorhus
@license MIT
*/


/* eslint-disable no-unused-vars */
var getOwnPropertySymbols = Object.getOwnPropertySymbols;
var hasOwnProperty = Object.prototype.hasOwnProperty;
var propIsEnumerable = Object.prototype.propertyIsEnumerable;

function toObject(val) {
	if (val === null || val === undefined) {
		throw new TypeError('Object.assign cannot be called with null or undefined');
	}

	return Object(val);
}

function shouldUseNative() {
	try {
		if (!Object.assign) {
			return false;
		}

		// Detect buggy property enumeration order in older V8 versions.

		// https://bugs.chromium.org/p/v8/issues/detail?id=4118
		var test1 = new String('abc');  // eslint-disable-line no-new-wrappers
		test1[5] = 'de';
		if (Object.getOwnPropertyNames(test1)[0] === '5') {
			return false;
		}

		// https://bugs.chromium.org/p/v8/issues/detail?id=3056
		var test2 = {};
		for (var i = 0; i < 10; i++) {
			test2['_' + String.fromCharCode(i)] = i;
		}
		var order2 = Object.getOwnPropertyNames(test2).map(function (n) {
			return test2[n];
		});
		if (order2.join('') !== '0123456789') {
			return false;
		}

		// https://bugs.chromium.org/p/v8/issues/detail?id=3056
		var test3 = {};
		'abcdefghijklmnopqrst'.split('').forEach(function (letter) {
			test3[letter] = letter;
		});
		if (Object.keys(Object.assign({}, test3)).join('') !==
				'abcdefghijklmnopqrst') {
			return false;
		}

		return true;
	} catch (err) {
		// We don't expect any of the above to throw, but better to be safe.
		return false;
	}
}

module.exports = shouldUseNative() ? Object.assign : function (target, source) {
	var from;
	var to = toObject(target);
	var symbols;

	for (var s = 1; s < arguments.length; s++) {
		from = Object(arguments[s]);

		for (var key in from) {
			if (hasOwnProperty.call(from, key)) {
				to[key] = from[key];
			}
		}

		if (getOwnPropertySymbols) {
			symbols = getOwnPropertySymbols(from);
			for (var i = 0; i < symbols.length; i++) {
				if (propIsEnumerable.call(from, symbols[i])) {
					to[symbols[i]] = from[symbols[i]];
				}
			}
		}
	}

	return to;
};


/***/ }),

/***/ "./node_modules/react/cjs/react.development.js":
/*!*****************************************************!*\
  !*** ./node_modules/react/cjs/react.development.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/** @license React v17.0.2
 * react.development.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



if (true) {
  (function() {
'use strict';

var _assign = __webpack_require__(/*! object-assign */ "./node_modules/object-assign/index.js");

// TODO: this is special because it gets imported during build.
var ReactVersion = '17.0.2';

// ATTENTION
// When adding new symbols to this file,
// Please consider also adding to 'react-devtools-shared/src/backend/ReactSymbols'
// The Symbol used to tag the ReactElement-like types. If there is no native Symbol
// nor polyfill, then a plain number is used for performance.
var REACT_ELEMENT_TYPE = 0xeac7;
var REACT_PORTAL_TYPE = 0xeaca;
exports.Fragment = 0xeacb;
exports.StrictMode = 0xeacc;
exports.Profiler = 0xead2;
var REACT_PROVIDER_TYPE = 0xeacd;
var REACT_CONTEXT_TYPE = 0xeace;
var REACT_FORWARD_REF_TYPE = 0xead0;
exports.Suspense = 0xead1;
var REACT_SUSPENSE_LIST_TYPE = 0xead8;
var REACT_MEMO_TYPE = 0xead3;
var REACT_LAZY_TYPE = 0xead4;
var REACT_BLOCK_TYPE = 0xead9;
var REACT_SERVER_BLOCK_TYPE = 0xeada;
var REACT_FUNDAMENTAL_TYPE = 0xead5;
var REACT_SCOPE_TYPE = 0xead7;
var REACT_OPAQUE_ID_TYPE = 0xeae0;
var REACT_DEBUG_TRACING_MODE_TYPE = 0xeae1;
var REACT_OFFSCREEN_TYPE = 0xeae2;
var REACT_LEGACY_HIDDEN_TYPE = 0xeae3;

if (typeof Symbol === 'function' && Symbol.for) {
  var symbolFor = Symbol.for;
  REACT_ELEMENT_TYPE = symbolFor('react.element');
  REACT_PORTAL_TYPE = symbolFor('react.portal');
  exports.Fragment = symbolFor('react.fragment');
  exports.StrictMode = symbolFor('react.strict_mode');
  exports.Profiler = symbolFor('react.profiler');
  REACT_PROVIDER_TYPE = symbolFor('react.provider');
  REACT_CONTEXT_TYPE = symbolFor('react.context');
  REACT_FORWARD_REF_TYPE = symbolFor('react.forward_ref');
  exports.Suspense = symbolFor('react.suspense');
  REACT_SUSPENSE_LIST_TYPE = symbolFor('react.suspense_list');
  REACT_MEMO_TYPE = symbolFor('react.memo');
  REACT_LAZY_TYPE = symbolFor('react.lazy');
  REACT_BLOCK_TYPE = symbolFor('react.block');
  REACT_SERVER_BLOCK_TYPE = symbolFor('react.server.block');
  REACT_FUNDAMENTAL_TYPE = symbolFor('react.fundamental');
  REACT_SCOPE_TYPE = symbolFor('react.scope');
  REACT_OPAQUE_ID_TYPE = symbolFor('react.opaque.id');
  REACT_DEBUG_TRACING_MODE_TYPE = symbolFor('react.debug_trace_mode');
  REACT_OFFSCREEN_TYPE = symbolFor('react.offscreen');
  REACT_LEGACY_HIDDEN_TYPE = symbolFor('react.legacy_hidden');
}

var MAYBE_ITERATOR_SYMBOL = typeof Symbol === 'function' && Symbol.iterator;
var FAUX_ITERATOR_SYMBOL = '@@iterator';
function getIteratorFn(maybeIterable) {
  if (maybeIterable === null || typeof maybeIterable !== 'object') {
    return null;
  }

  var maybeIterator = MAYBE_ITERATOR_SYMBOL && maybeIterable[MAYBE_ITERATOR_SYMBOL] || maybeIterable[FAUX_ITERATOR_SYMBOL];

  if (typeof maybeIterator === 'function') {
    return maybeIterator;
  }

  return null;
}

/**
 * Keeps track of the current dispatcher.
 */
var ReactCurrentDispatcher = {
  /**
   * @internal
   * @type {ReactComponent}
   */
  current: null
};

/**
 * Keeps track of the current batch's configuration such as how long an update
 * should suspend for if it needs to.
 */
var ReactCurrentBatchConfig = {
  transition: 0
};

/**
 * Keeps track of the current owner.
 *
 * The current owner is the component who should own any components that are
 * currently being constructed.
 */
var ReactCurrentOwner = {
  /**
   * @internal
   * @type {ReactComponent}
   */
  current: null
};

var ReactDebugCurrentFrame = {};
var currentExtraStackFrame = null;
function setExtraStackFrame(stack) {
  {
    currentExtraStackFrame = stack;
  }
}

{
  ReactDebugCurrentFrame.setExtraStackFrame = function (stack) {
    {
      currentExtraStackFrame = stack;
    }
  }; // Stack implementation injected by the current renderer.


  ReactDebugCurrentFrame.getCurrentStack = null;

  ReactDebugCurrentFrame.getStackAddendum = function () {
    var stack = ''; // Add an extra top frame while an element is being validated

    if (currentExtraStackFrame) {
      stack += currentExtraStackFrame;
    } // Delegate to the injected renderer-specific implementation


    var impl = ReactDebugCurrentFrame.getCurrentStack;

    if (impl) {
      stack += impl() || '';
    }

    return stack;
  };
}

/**
 * Used by act() to track whether you're inside an act() scope.
 */
var IsSomeRendererActing = {
  current: false
};

var ReactSharedInternals = {
  ReactCurrentDispatcher: ReactCurrentDispatcher,
  ReactCurrentBatchConfig: ReactCurrentBatchConfig,
  ReactCurrentOwner: ReactCurrentOwner,
  IsSomeRendererActing: IsSomeRendererActing,
  // Used by renderers to avoid bundling object-assign twice in UMD bundles:
  assign: _assign
};

{
  ReactSharedInternals.ReactDebugCurrentFrame = ReactDebugCurrentFrame;
}

// by calls to these methods by a Babel plugin.
//
// In PROD (or in packages without access to React internals),
// they are left as they are instead.

function warn(format) {
  {
    for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
      args[_key - 1] = arguments[_key];
    }

    printWarning('warn', format, args);
  }
}
function error(format) {
  {
    for (var _len2 = arguments.length, args = new Array(_len2 > 1 ? _len2 - 1 : 0), _key2 = 1; _key2 < _len2; _key2++) {
      args[_key2 - 1] = arguments[_key2];
    }

    printWarning('error', format, args);
  }
}

function printWarning(level, format, args) {
  // When changing this logic, you might want to also
  // update consoleWithStackDev.www.js as well.
  {
    var ReactDebugCurrentFrame = ReactSharedInternals.ReactDebugCurrentFrame;
    var stack = ReactDebugCurrentFrame.getStackAddendum();

    if (stack !== '') {
      format += '%s';
      args = args.concat([stack]);
    }

    var argsWithFormat = args.map(function (item) {
      return '' + item;
    }); // Careful: RN currently depends on this prefix

    argsWithFormat.unshift('Warning: ' + format); // We intentionally don't use spread (or .apply) directly because it
    // breaks IE9: https://github.com/facebook/react/issues/13610
    // eslint-disable-next-line react-internal/no-production-logging

    Function.prototype.apply.call(console[level], console, argsWithFormat);
  }
}

var didWarnStateUpdateForUnmountedComponent = {};

function warnNoop(publicInstance, callerName) {
  {
    var _constructor = publicInstance.constructor;
    var componentName = _constructor && (_constructor.displayName || _constructor.name) || 'ReactClass';
    var warningKey = componentName + "." + callerName;

    if (didWarnStateUpdateForUnmountedComponent[warningKey]) {
      return;
    }

    error("Can't call %s on a component that is not yet mounted. " + 'This is a no-op, but it might indicate a bug in your application. ' + 'Instead, assign to `this.state` directly or define a `state = {};` ' + 'class property with the desired state in the %s component.', callerName, componentName);

    didWarnStateUpdateForUnmountedComponent[warningKey] = true;
  }
}
/**
 * This is the abstract API for an update queue.
 */


var ReactNoopUpdateQueue = {
  /**
   * Checks whether or not this composite component is mounted.
   * @param {ReactClass} publicInstance The instance we want to test.
   * @return {boolean} True if mounted, false otherwise.
   * @protected
   * @final
   */
  isMounted: function (publicInstance) {
    return false;
  },

  /**
   * Forces an update. This should only be invoked when it is known with
   * certainty that we are **not** in a DOM transaction.
   *
   * You may want to call this when you know that some deeper aspect of the
   * component's state has changed but `setState` was not called.
   *
   * This will not invoke `shouldComponentUpdate`, but it will invoke
   * `componentWillUpdate` and `componentDidUpdate`.
   *
   * @param {ReactClass} publicInstance The instance that should rerender.
   * @param {?function} callback Called after component is updated.
   * @param {?string} callerName name of the calling function in the public API.
   * @internal
   */
  enqueueForceUpdate: function (publicInstance, callback, callerName) {
    warnNoop(publicInstance, 'forceUpdate');
  },

  /**
   * Replaces all of the state. Always use this or `setState` to mutate state.
   * You should treat `this.state` as immutable.
   *
   * There is no guarantee that `this.state` will be immediately updated, so
   * accessing `this.state` after calling this method may return the old value.
   *
   * @param {ReactClass} publicInstance The instance that should rerender.
   * @param {object} completeState Next state.
   * @param {?function} callback Called after component is updated.
   * @param {?string} callerName name of the calling function in the public API.
   * @internal
   */
  enqueueReplaceState: function (publicInstance, completeState, callback, callerName) {
    warnNoop(publicInstance, 'replaceState');
  },

  /**
   * Sets a subset of the state. This only exists because _pendingState is
   * internal. This provides a merging strategy that is not available to deep
   * properties which is confusing. TODO: Expose pendingState or don't use it
   * during the merge.
   *
   * @param {ReactClass} publicInstance The instance that should rerender.
   * @param {object} partialState Next partial state to be merged with state.
   * @param {?function} callback Called after component is updated.
   * @param {?string} Name of the calling function in the public API.
   * @internal
   */
  enqueueSetState: function (publicInstance, partialState, callback, callerName) {
    warnNoop(publicInstance, 'setState');
  }
};

var emptyObject = {};

{
  Object.freeze(emptyObject);
}
/**
 * Base class helpers for the updating state of a component.
 */


function Component(props, context, updater) {
  this.props = props;
  this.context = context; // If a component has string refs, we will assign a different object later.

  this.refs = emptyObject; // We initialize the default updater but the real one gets injected by the
  // renderer.

  this.updater = updater || ReactNoopUpdateQueue;
}

Component.prototype.isReactComponent = {};
/**
 * Sets a subset of the state. Always use this to mutate
 * state. You should treat `this.state` as immutable.
 *
 * There is no guarantee that `this.state` will be immediately updated, so
 * accessing `this.state` after calling this method may return the old value.
 *
 * There is no guarantee that calls to `setState` will run synchronously,
 * as they may eventually be batched together.  You can provide an optional
 * callback that will be executed when the call to setState is actually
 * completed.
 *
 * When a function is provided to setState, it will be called at some point in
 * the future (not synchronously). It will be called with the up to date
 * component arguments (state, props, context). These values can be different
 * from this.* because your function may be called after receiveProps but before
 * shouldComponentUpdate, and this new state, props, and context will not yet be
 * assigned to this.
 *
 * @param {object|function} partialState Next partial state or function to
 *        produce next partial state to be merged with current state.
 * @param {?function} callba