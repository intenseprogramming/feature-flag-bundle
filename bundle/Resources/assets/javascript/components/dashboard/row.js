import React from 'react';
import PropTypes from 'prop-types';
import FeatureFlagHandler from '../../handlers/feature_flag_handler';

export default class Row extends React.Component {
    onStateChange(event) {
        const feature = this.props.featureDefinition;

        switch (event.target.value) {
        case 'unset':
            FeatureFlagHandler.resetFeatureFlag(feature.identifier, feature.scope, this.props.onFeatureChange);
            break;
        case 'enabled':
        case 'disabled':
            FeatureFlagHandler.updateFeatureFlag(feature.identifier, feature.scope, event.target.value === 'enabled', this.props.onFeatureChange);
            break;
        }
    }

    render() {
        const feature = this.props.featureDefinition;

        return (
            <tr>
                <td>{feature.name}</td>
                <td>{feature.description}</td>
                <td>{window.Translator.trans(feature.fromEnabled ? 'feature.enabled' : 'feature.disabled', {}, 'feature_flag')}</td>
                <td>{window.Translator.trans(`feature.scope.${feature.fromScope}`, {}, 'feature_flag')}</td>
                <td>
                    <select className="form-control" disabled={this.props.loading} onChange={this.onStateChange.bind(this)}>
                        <option value="unset" selected={feature.enabled === null}>
                            {window.Translator.trans('feature.inherited', {}, 'feature_flag')}
                        </option>
                        <option value="enabled" selected={feature.enabled === true}>
                            {window.Translator.trans('feature.enabled', {}, 'feature_flag')}
                        </option>
                        <option value="disabled" selected={feature.enabled === false}>
                            {window.Translator.trans('feature.disabled', {}, 'feature_flag')}
                        </option>
                    </select>
                </td>
            </tr>
        );
    }
}

Row.propTypes = {
    featureDefinition: PropTypes.shape({
        description: PropTypes.string.isRequired,
        enabled: PropTypes.bool.isRequired,
        scope: PropTypes.string.isRequired,
        fromEnabled: PropTypes.bool.isRequired,
        fromScope: PropTypes.string.isRequired,
        identifier: PropTypes.string.isRequired,
        name: PropTypes.string.isRequired,
        default: PropTypes.bool.isRequired,
    }).isRequired,
    onFeatureChange: PropTypes.func.isRequired,
    loading: PropTypes.bool.isRequired,
};