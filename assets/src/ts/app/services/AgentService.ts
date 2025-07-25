import $ from 'jquery';
import {WpLocalizedParams, ApiError, ApiResponse} from '../../types';

declare const wssa_params: WpLocalizedParams;

class AgentService {
    public generateSeo(requestMessage: string, conversationHistory: string[]): Promise<ApiResponse> {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: wssa_params.rest_url,
                method: 'POST',
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-Nonce', wssa_params.nonce);
                },
                data: {
                    product_id: wssa_params.product_id,
                    request_message: requestMessage,
                    conversation_history: conversationHistory
                }
            }).done((response) => {
                if (response) {
                    resolve(response as ApiResponse);
                } else {
                    reject({ message: 'Invalid API response structure.' } as ApiError);
                }
            }).fail((jqXHR) => {
                const error: ApiError = jqXHR.responseJSON || { message: 'An unknown error occurred.' };
                reject(error);
            });
        });
    }
}

export const agentService = new AgentService();
