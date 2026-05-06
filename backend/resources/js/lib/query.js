import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { apiRequest, collectionFrom } from './api';

export function useApiQuery(queryKey, endpoint, options = {}) {
    return useQuery({
        queryKey,
        queryFn: () => apiRequest(endpoint),
        retry: false,
        staleTime: 30000,
        ...options,
    });
}

export function useCollectionQuery(queryKey, endpoint, options = {}) {
    return useApiQuery(queryKey, endpoint, {
        select: collectionFrom,
        ...options,
    });
}

export function useApiMutation({ invalidate = [], ...mutationOptions } = {}) {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: ({ endpoint, method = 'POST', body }) => apiRequest(endpoint, {
            method,
            body: body instanceof FormData ? body : JSON.stringify(body || {}),
        }),
        onSuccess: (...args) => {
            invalidate.forEach((queryKey) => queryClient.invalidateQueries({ queryKey }));
            mutationOptions.onSuccess?.(...args);
        },
        ...mutationOptions,
    });
}
